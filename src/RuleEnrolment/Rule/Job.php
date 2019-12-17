<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ilCronJob;
use ilCronJobResult;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use Throwable;

/**
 * Class Job
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Job extends ilCronJob
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var int|null
     */
    protected $parent_id = null;


    /**
     * Job constructor
     *
     * @param int|null $parent_id
     */
    public function __construct(/*?*/ int $parent_id = null)
    {
        $this->parent_id = $parent_id;
    }


    /**
     * Get id
     *
     * @return string
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        return ilSrUserEnrolmentPlugin::PLUGIN_NAME;
    }


    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "";
    }


    /**
     * Is to be activated on "installation"
     *
     * @return boolean
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * Can the schedule be configured?
     *
     * @return boolean
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * Get schedule type
     *
     * @return int
     */
    public function getDefaultScheduleType() : int
    {
        return self::SCHEDULE_TYPE_IN_HOURS;
    }


    /**
     * Get schedule value
     *
     * @return int|array
     */
    public function getDefaultScheduleValue() : int
    {
        return 12;
    }


    /**
     * Run job
     *
     * @return ilCronJobResult
     */
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $rules = self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRules(AbstractRule::PARENT_CONTEXT_COURSE, AbstractRule::TYPE_COURSE_RULE, $this->parent_id);

        $objects = [];

        foreach ($rules as $rule) {

            $objects[$rule->getParentId()] = true;

            foreach (self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newCheckerInstance($rule)->getCheckedObjectsUsers() as $object_user) {
                try {
                    if (self::srUserEnrolment()->ruleEnrolment()->enrollMemberToCourse($rule->getParentId(), $object_user->user_id)) {
                        self::srUserEnrolment()->ruleEnrolment()->logs()->storeLog(self::srUserEnrolment()
                            ->ruleEnrolment()
                            ->logs()
                            ->factory()
                            ->newObjectRuleUserInstance($rule->getParentId(), $object_user->user_id, $rule->getId())
                            ->withStatus(Log::STATUS_ENROLLED));
                    }
                } catch (Throwable $ex) {
                    self::srUserEnrolment()->ruleEnrolment()->logs()->storeLog(self::srUserEnrolment()->ruleEnrolment()->logs()->factory()
                        ->newExceptionInstance($ex, $rule->getParentId(), $object_user->user_id, $rule->getId())->withStatus(Log::STATUS_ENROLL_FAILED));
                }
            }
        }

        $logs = array_reduce(Log::$status_enroll, function (array $logs, int $status) : array {
            $logs[] = self::plugin()->translate("status_" . $status, LogsGUI::LANG_MODULE) . ": " . count(self::srUserEnrolment()->ruleEnrolment()->logs()->getKeptLogs($status));

            return $logs;
        }, [
            self::plugin()->translate("rules", LogsGUI::LANG_MODULE) . ": " . count($rules),
            self::plugin()->translate("objects", LogsGUI::LANG_MODULE) . ": " . count($objects)
        ]);

        $result_count = implode("<br>", $logs);

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage($result_count);

        return $result;
    }
}
