<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\RulesCourseSettingsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesCourseGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI: ilUIPluginRouterGUI
 */
class RulesCourseGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_BACK = "back";
    const CMD_RUN_RULES = "runRules";
    const CMD_SORT_BY_ENROLL_TYPE = "sortByEnrollType";
    const GET_PARAM_OBJ_SINGLE_ID = "obj_single_id";
    const GET_PARAM_REF_ID = "ref_id";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_RULES = "rules";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var int|null
     */
    protected $obj_single_id = null;


    /**
     * RulesCourseGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     */
    public static function addTabs(int $obj_ref_id,/*?*/ int $obj_single_id = null) : void
    {
        if (self::srUserEnrolment()->ruleEnrolment()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $obj_single_id)) {
            self::dic()->ctrl()->setParameterByClass(static::class, self::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()->ctrl()->setParameterByClass(static::class, self::GET_PARAM_OBJ_SINGLE_ID, $obj_single_id);

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(static::getTitle(), str_replace("\\", "\\\\", self::dic()
                ->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    static::class
                ], RulesGUI::CMD_LIST_RULES))));
        }
    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     *
     * @return int
     */
    public static function getObjId(int $obj_ref_id,/*?*/ int $obj_single_id = null) : int
    {
        if (!empty($obj_single_id)) {
            return $obj_single_id;
        } else {
            return self::dic()->objDataCache()->lookupObjId($obj_ref_id);
        }
    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     *
     * @return string
     */
    public static function getObjType(int $obj_ref_id,/*?*/ int $obj_single_id = null) : string
    {
        return self::dic()->objDataCache()->lookupType(static::getObjId($obj_ref_id, $obj_single_id));
    }


    /**
     * @return string
     */
    public static function getTitle() : string
    {
        return self::plugin()->translate("type_course_rule", RulesGUI::LANG_MODULE);
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));
        $this->obj_single_id = (intval(filter_input(INPUT_GET, self::GET_PARAM_OBJ_SINGLE_ID)) ?? null);

        if (!self::srUserEnrolment()->ruleEnrolment()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id, $this->obj_single_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_OBJ_SINGLE_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RulesGUI::class):
                self::dic()->ctrl()->forwardCommand(new RulesGUI($this->getRuleContext(), static::getObjId($this->obj_ref_id, $this->obj_single_id)));

                self::dic()->ui()->mainTemplate()->printToStdout();
                break;

            case strtolower(LogsGUI::class):
                self::dic()->ctrl()->forwardCommand(new LogsGUI(static::getObjId($this->obj_ref_id, $this->obj_single_id)));
                break;

            case strtolower(RulesCourseSettingsGUI::class):
                self::dic()->ctrl()->forwardCommand(new RulesCourseSettingsGUI($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case RulesGUI::CMD_LIST_RULES:
                    case self::CMD_RUN_RULES:
                    case self::CMD_SORT_BY_ENROLL_TYPE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return string
     */
    public function getBackTitle() : string
    {
        return self::dic()->objDataCache()->lookupTitle(static::getObjId($this->obj_ref_id, $this->obj_single_id));
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @return int|null
     */
    public function getObjSingleId() : ?int
    {
        return $this->obj_single_id;
    }


    /**
     * @return int
     */
    public function getRuleContext() : int
    {
        return AbstractRule::PARENT_CONTEXT_COURSE;
    }


    /**
     * @return int
     */
    public function getRuleType() : int
    {
        return AbstractRule::TYPE_COURSE_RULE;
    }


    /**
     *
     */
    protected function back() : void
    {
        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, self::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class,
            ilCourseMembershipGUI::class
        ]);
    }


    /**
     *
     */
    protected function listRules() : void
    {
        self::dic()->ctrl()->setParameterByClass(RulesGUI::class, RulesGUI::GET_PARAM_TYPE . $this->getRuleContext(), $this->getRuleType());

        self::dic()->ctrl()->redirectByClass(RulesGUI::class, RulesGUI::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function runRules() : void
    {
        $result_count = self::srUserEnrolment()->ruleEnrolment()->rules()->factory()->newJobInstance([
            [
                $this->getRuleContext(),
                $this->getRuleType(),
                static::getObjId($this->obj_ref_id, $this->obj_single_id)
            ]
        ], false, [])->run()->getMessage();

        ilUtil::sendInfo($result_count, true);

        self::dic()->ctrl()->redirect($this, RulesGUI::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        self::dic()->tabs()->setBackTarget($this->getBackTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_BACK));

        RulesGUI::addTabs($this->getRuleContext());
        LogsGUI::addTabs();
        RulesCourseSettingsGUI::addTabs();

        if (self::dic()->ctrl()->getCmd() === RulesGUI::CMD_LIST_RULES) {
            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("run_rules", RulesGUI::LANG_MODULE),
                str_replace("\\", "\\\\", self::dic()->ctrl()->getLinkTarget($this, self::CMD_RUN_RULES))));

            if ($this->getRuleType() === AbstractRule::TYPE_COURSE_RULE) {
                self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("sort_by_enroll_type", RulesGUI::LANG_MODULE),
                    str_replace("\\", "\\\\", self::dic()->ctrl()->getLinkTarget($this, self::CMD_SORT_BY_ENROLL_TYPE))));
            }
        }
    }


    /**
     *
     */
    protected function sortByEnrollType() : void
    {
        if ($this->getRuleType() === AbstractRule::TYPE_COURSE_RULE) {
            self::srUserEnrolment()->enrolmentWorkflow()->rules()->sortByEnrollType($this->getRuleContext(), static::getObjId($this->obj_ref_id, $this->obj_single_id));

            ilUtil::sendSuccess(self::plugin()->translate("sorted_by_enroll_type", RulesGUI::LANG_MODULE), true);
        }

        self::dic()->ctrl()->redirect($this, RulesGUI::CMD_LIST_RULES);
    }
}
