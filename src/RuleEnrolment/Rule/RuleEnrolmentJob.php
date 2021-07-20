<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ilCheckboxInputGUI;
use ilCronJob;
use ilCronJobResult;
use ilCronManager;
use ilPropertyFormGUI;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;
use Throwable;

/**
 * Class RuleEnrolmentJob
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
 */
class RuleEnrolmentJob extends ilCronJob
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID;
    const KEY_CONTINUE_ON_CRASH = "continue_on_crash";
    const KEY_CONTINUE_ON_CRASH_RULES = "continue_on_crash_rules";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var bool|null
     */
    protected $continue_on_crash = null;
    /**
     * @var array|null
     */
    protected $continue_on_crash_rules = null;
    /**
     * @var array|null
     */
    protected $parents = null;


    /**
     * RuleEnrolmentJob constructor
     *
     * @param array|null $parents
     * @param bool|null  $continue_on_crash
     * @param array|null $continue_on_crash_rules
     */
    public function __construct(/*?*/ array $parents = null, /*?*/ bool $continue_on_crash = null, /*?*/ array $continue_on_crash_rules = null)
    {
        $this->parents = $parents;
        $this->continue_on_crash = $continue_on_crash;
        $this->continue_on_crash_rules = $continue_on_crash_rules;
    }


    /**
     * @inheritDoc
     */
    public function addCustomSettingsToForm(ilPropertyFormGUI $a_form) : void
    {
        $continue_on_crash = new ilCheckboxInputGUI(self::plugin()->translate(self::KEY_CONTINUE_ON_CRASH, RulesGUI::LANG_MODULE), self::KEY_CONTINUE_ON_CRASH);
        $continue_on_crash->setInfo(nl2br(self::plugin()->translate(self::KEY_CONTINUE_ON_CRASH . "_info", RulesGUI::LANG_MODULE), false));
        $continue_on_crash->setChecked($this->isContinueOnCrash());
        $a_form->addItem($continue_on_crash);
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType() : int
    {
        return self::SCHEDULE_TYPE_IN_HOURS;
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleValue() : ?int
    {
        return 12;
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("type_course_rule", RulesGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @inheritDoc
     */
    public function getTitle() : string
    {
        return ilSrUserEnrolmentPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("type_course_rule", RulesGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasCustomSettings() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        if (!self::srUserEnrolment()->ruleEnrolment()->isEnabled()) {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);

            return $result;
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_reduce($this->getParents(), function (array $rules, array $parent) : array {
            $rules = array_merge($rules, self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRules($parent[0], $parent[1], $parent[2]));

            return $rules;
        }, []);

        $continue_on_crash_rules = $this->getContinueOnCrashRules();
        $count_continue_on_crash_rules = 0;
        if ($this->isContinueOnCrash()) {
            $count_continue_on_crash_rules = count($rules);
            $rules = array_filter($rules, function (AbstractRule $rule) use ($continue_on_crash_rules) : bool {
                return !in_array($rule->getRuleId(), $continue_on_crash_rules);
            });
            $count_continue_on_crash_rules = $count_continue_on_crash_rules - count($rules);
        }

        $objects = [];

        foreach ($rules as $rule) {
            try {
                $objects[$rule->getParentContext() . "_" . $rule->getParentId()] = true;

                $settings = self::srUserEnrolment()->ruleEnrolment()->rules()->settings()->getSettings($rule->getParentId());

                $checked_object_users = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newCheckerInstance($rule)->getCheckedObjectsUsers();

                foreach ($checked_object_users as $object_user) {
                    try {
                        if (!self::srUserEnrolment()->ruleEnrolment()->isEnrolled($rule->getParentId(), $object_user->user_id)) {
                            if (self::srUserEnrolment()->ruleEnrolment()->enroll($rule->getParentId(), $object_user->user_id, $rule->getEnrollType())) {
                                self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()
                                    ->logs()
                                    ->factory()
                                    ->newObjectRuleUserInstance($rule->getParentId(), $object_user->user_id, $rule->getId())
                                    ->withStatus(Log::STATUS_ENROLLED));
                            }
                        } else {
                            if ($settings->isUpdateEnrollType()) {
                                if ($rule->getEnrollType() !== self::srUserEnrolment()->ruleEnrolment()->getEnrolledType($rule->getParentId(), $object_user->user_id)) {
                                    if (self::srUserEnrolment()->ruleEnrolment()->unenroll($rule->getParentId(), $object_user->user_id)
                                        && self::srUserEnrolment()
                                            ->ruleEnrolment()
                                            ->enroll($rule->getParentId(), $object_user->user_id, $rule->getEnrollType())
                                    ) {
                                        self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()
                                            ->logs()
                                            ->factory()
                                            ->newObjectRuleUserInstance($rule->getParentId(), $object_user->user_id, $rule->getId())
                                            ->withStatus(Log::STATUS_ENROLL_UPDATED));
                                    }
                                }
                            }
                        }
                    } catch (Throwable $ex) {
                        self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()->factory()
                            ->newExceptionInstance($ex, $rule->getParentId(), $object_user->user_id, $rule->getId())->withStatus(Log::STATUS_ENROLL_FAILED));
                    }

                    ilCronManager::ping($this->getId());
                }

                if ($settings->isUnenroll()) {
                    $object_members = self::srUserEnrolment()->ruleEnrolment()->getEnrolleds($rule->getParentId());

                    foreach ($object_members as $object_member) {
                        try {
                            if (empty(array_filter($checked_object_users, function (stdClass $object_user) use ($object_member) : bool {
                                return ($object_user->user_id === intval($object_member));
                            }))
                            ) {
                                if ($rule->getEnrollType() === self::srUserEnrolment()->ruleEnrolment()->getEnrolledType($rule->getParentId(), $object_member)) {
                                    if (self::srUserEnrolment()->ruleEnrolment()->unenroll($rule->getParentId(), $object_member)) {
                                        self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()
                                            ->logs()
                                            ->factory()
                                            ->newObjectRuleUserInstance($rule->getParentId(), $object_member, $rule->getId())
                                            ->withStatus(Log::STATUS_UNENROLLED));
                                    }
                                }
                            }
                        } catch (Throwable $ex) {
                            self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()->factory()
                                ->newExceptionInstance($ex, $rule->getParentId(), $object_member, $rule->getId())->withStatus(Log::STATUS_UNENROLL_FAILED));
                        }

                        ilCronManager::ping($this->getId());
                    }
                }

                $this->addContinueOnCrashRule($rule->getRuleId());
            } catch (Throwable $ex) {
                self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()->factory()
                    ->newExceptionInstance($ex, $rule->getParentId(), null, $rule->getId())->withStatus(Log::STATUS_ENROLL_FAILED));
            }

            ilCronManager::ping($this->getId());
        }

        $this->setContinueOnCrashRules([]);

        $logs = array_reduce(array_keys(Log::$status_enroll), function (array $logs, int $status) : array {
            $logs[] = self::plugin()->translate("status_" . Log::$status_enroll[$status], LogsGUI::LANG_MODULE) . ": " . count(self::srUserEnrolment()->logs()->getKeptLogs($status));

            return $logs;
        }, [
            self::plugin()->translate("rules", LogsGUI::LANG_MODULE) . ": " . count($rules),
            self::plugin()->translate("objects", LogsGUI::LANG_MODULE) . ": " . count($objects)
        ]);
        if ($this->isContinueOnCrash() && !empty($count_continue_on_crash_rules)) {
            array_unshift($logs, self::plugin()->translate(self::KEY_CONTINUE_ON_CRASH . "_count", RulesGUI::LANG_MODULE) . ": " . $count_continue_on_crash_rules);
        }

        $result_count = nl2br(implode("\n", $logs), false);

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage($result_count);

        return $result;
    }


    /**
     * @inheritDoc
     */
    public function saveCustomSettings(ilPropertyFormGUI $a_form) : bool
    {
        self::srUserEnrolment()->config()->setValue(self::KEY_CONTINUE_ON_CRASH, boolval($a_form->getInput(self::KEY_CONTINUE_ON_CRASH)));
        $this->setContinueOnCrashRules([]);

        return true;
    }


    /**
     * @param int $continue_on_crash_rule
     */
    protected function addContinueOnCrashRule(int $continue_on_crash_rule) : void
    {
        if ($this->isContinueOnCrash()) {
            $this->continue_on_crash_rules[] = $continue_on_crash_rule;

            $this->saveContinueOnCrashRules();
        }
    }


    /**
     * @return array
     */
    protected function getContinueOnCrashRules() : array
    {
        if ($this->continue_on_crash_rules === null) {
            $this->continue_on_crash_rules = self::srUserEnrolment()->config()->getValue(self::KEY_CONTINUE_ON_CRASH_RULES) ?? [];
        }

        return $this->continue_on_crash_rules;
    }


    /**
     * @param array $continue_on_crash_rules
     */
    protected function setContinueOnCrashRules(array $continue_on_crash_rules) : void
    {
        if ($this->isContinueOnCrash()) {
            $this->continue_on_crash_rules = $continue_on_crash_rules;

            $this->saveContinueOnCrashRules();
        }
    }


    /**
     * @return array
     */
    protected function getParents() : array
    {
        if ($this->parents === null) {
            $this->parents = array_reduce(AbstractRule::ENROLL_BY_USER, function (array $parents, int $context) : array {
                $parents = array_merge($parents, array_map(function (int $type) use ($context) : array {
                    return [
                        $context,
                        $type,
                        null
                    ];
                }, array_keys(AbstractRule::TYPES[$context])));

                return $parents;
            }, []);
        }

        return $this->parents;
    }


    /**
     * @return bool
     */
    protected function isContinueOnCrash() : bool
    {
        if ($this->continue_on_crash === null) {
            $this->continue_on_crash = self::srUserEnrolment()->config()->getValue(self::KEY_CONTINUE_ON_CRASH);
        }

        return $this->continue_on_crash;
    }


    /**
     *
     */
    protected function saveContinueOnCrashRules() : void
    {
        self::srUserEnrolment()->config()->setValue(self::KEY_CONTINUE_ON_CRASH_RULES, $this->continue_on_crash_rules);
    }
}
