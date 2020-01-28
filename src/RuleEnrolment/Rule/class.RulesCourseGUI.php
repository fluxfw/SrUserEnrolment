<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesCourseGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI
 */
class RulesCourseGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_RUN_RULES = "runRules";
    const GET_PARAM_REF_ID = "ref_id";
    const TAB_RULES = "rules";
    /**
     * @var int
     */
    protected $obj_ref_id;


    /**
     * RulesCourseGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));

        if (!self::srUserEnrolment()->ruleEnrolment()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RulesGUI::class):
                self::dic()->ctrl()->forwardCommand(new RulesGUI(AbstractRule::PARENT_CONTEXT_COURSE, self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)));

                // TODO: Use DICTrait
                if (self::version()->is60()) {
                    self::dic()->ui()->mainTemplate()->printToStdout();
                } else {
                    self::dic()->ui()->mainTemplate()->show();
                }
                break;

            case strtolower(LogsGUI::class):
                self::dic()->ctrl()->forwardCommand(new LogsGUI($this->obj_ref_id));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case RulesGUI::CMD_LIST_RULES:
                    case self::CMD_RUN_RULES:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*:void*/
    {
        if (self::srUserEnrolment()->ruleEnrolment()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->tabs()->addSubTab(self::TAB_RULES, self::plugin()->translate("type_course_rule", RulesGUI::LANG_MODULE), self::dic()
                ->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    self::class
                ], RulesGUI::CMD_LIST_RULES));
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->language()->loadLanguageModule("crs");

        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_BACK));

        RulesGUI::addTabs(AbstractRule::PARENT_CONTEXT_COURSE);
        LogsGUI::addTabs();

        if (self::dic()->ctrl()->getCmd() === RulesGUI::CMD_LIST_RULES) {
            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("run_rules", RulesGUI::LANG_MODULE),
                self::dic()->ctrl()->getLinkTarget($this, self::CMD_RUN_RULES)));
        }
    }


    /**
     *
     */
    protected function back()/*: void*/
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
    protected function listRules()/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(RulesGUI::class, RulesGUI::GET_PARAM_TYPE . AbstractRule::PARENT_CONTEXT_COURSE, AbstractRule::TYPE_COURSE_RULE);

        self::dic()->ctrl()->redirectByClass(RulesGUI::class, RulesGUI::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function runRules()/*: void*/
    {
        $result_count = self::srUserEnrolment()->ruleEnrolment()->rules()->factory()->newJobInstance(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))->run()->getMessage();

        ilUtil::sendInfo($result_count, true);

        self::dic()->ctrl()->redirect($this, RulesGUI::CMD_LIST_RULES);
    }
}
