<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class StepsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowGUI
 */
class StepsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_DISABLE_STEPS = "disableSteps";
    const CMD_ENABLE_STEPS = "enableSteps";
    const CMD_LIST_STEPS = "listSteps";
    const CMD_REMOVE_STEPS = "removeSteps";
    const CMD_REMOVE_STEPS_CONFIRM = "removeStepsConfirm";
    const LANG_MODULE = "steps";
    const TAB_LIST_STEPS = "list_steps";
    /**
     * @var WorkflowGUI
     */
    protected $parent;


    /**
     * StepsGUI constructor
     *
     * @param WorkflowGUI $parent
     */
    public function __construct(WorkflowGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(StepGUI::class);
                self::dic()->ctrl()->forwardCommand(new StepGUI($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_DISABLE_STEPS:
                    case self::CMD_ENABLE_STEPS:
                    case self::CMD_LIST_STEPS:
                    case self::CMD_REMOVE_STEPS:
                    case self::CMD_REMOVE_STEPS_CONFIRM:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    public static function addTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_STEPS, self::plugin()->translate("steps", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_STEPS));
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     *
     */
    protected function listSteps()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_STEPS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newTableInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function enableSteps()/*: void*/
    {
        $step_ids = filter_input(INPUT_POST, StepGUI::GET_PARAM_STEP_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($step_ids)) {
            $step_ids = [];
        }

        /**
         * @var Step[] $steps
         */
        $steps = array_map(function (int $step_id) : Step {
            return self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($step_id);
        }, $step_ids);

        foreach ($steps as $step) {
            $step->setEnabled(true);

            $step->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("enabled_steps", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_STEPS);
    }


    /**
     *
     */
    protected function disableSteps()/*: void*/
    {
        $step_ids = filter_input(INPUT_POST, StepGUI::GET_PARAM_STEP_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($step_ids)) {
            $step_ids = [];
        }

        /**
         * @var Step[] $steps
         */
        $steps = array_map(function (int $step_id) : Step {
            return self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($step_id);
        }, $step_ids);

        foreach ($steps as $step) {
            $step->setEnabled(false);

            $step->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("disabled_steps", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_STEPS);
    }


    /**
     *
     */
    protected function removeStepsConfirm()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_STEPS);

        $step_ids = filter_input(INPUT_POST, StepGUI::GET_PARAM_STEP_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($step_ids)) {
            $step_ids = [];
        }

        /**
         * @var Step[] $steps
         */
        $steps = array_map(function (int $step_id) : Step {
            return self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($step_id);
        }, $step_ids);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_steps_confirm", self::LANG_MODULE));

        foreach ($steps as $step) {
            $confirmation->addItem(StepGUI::GET_PARAM_STEP_ID . "[]", $step->getStepId(), $step->getTitle());
        }

        $confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE), self::CMD_REMOVE_STEPS);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_LIST_STEPS);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function removeSteps()/*: void*/
    {
        $step_ids = filter_input(INPUT_POST, StepGUI::GET_PARAM_STEP_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($step_ids)) {
            $step_ids = [];
        }

        /**
         * @var Step[] $steps
         */
        $steps = array_map(function (int $step_id) : Step {
            return self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($step_id);
        }, $step_ids);

        foreach ($steps as $step) {
            self::srUserEnrolment()->enrolmentWorkflow()->steps()->deleteStep($step);
        }

        ilUtil::sendSuccess(self::plugin()->translate("removed_steps", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_STEPS);
    }


    /**
     * @return WorkflowGUI
     */
    public function getParent() : WorkflowGUI
    {
        return $this->parent;
    }
}
