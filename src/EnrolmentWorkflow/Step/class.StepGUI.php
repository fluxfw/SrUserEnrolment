<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;

/**
 * Class StepGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI
 * @ilCtrl_isCalledBy srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI
 */
class StepGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_ADD_STEP = "addStep";
    const CMD_BACK = "back";
    const CMD_CREATE_STEP = "createStep";
    const CMD_EDIT_STEP = "editStep";
    const CMD_MOVE_STEP_DOWN = "moveStepDown";
    const CMD_MOVE_STEP_UP = "moveStepUp";
    const CMD_REMOVE_STEP = "removeStep";
    const CMD_REMOVE_STEP_CONFIRM = "removeStepConfirm";
    const CMD_UPDATE_STEP = "updateStep";
    const GET_PARAM_STEP_ID = "step_id";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_EDIT_STEP = "edit_step";
    /**
     * @var StepsGUI
     */
    protected $parent;
    /**
     * @var Step
     */
    protected $step;


    /**
     * StepGUI constructor
     *
     * @param StepsGUI $parent
     */
    public function __construct(StepsGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById(intval(filter_input(INPUT_GET, self::GET_PARAM_STEP_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_STEP_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RulesGUI::class):
                self::dic()->ctrl()->forwardCommand(new RulesGUI(AbstractRule::PARENT_CONTEXT_STEP, $this->step->getStepId()));
                break;

            case strtolower(ActionsGUI::class):
                self::dic()->ctrl()->forwardCommand(new ActionsGUI($this));
                break;

            case strtolower(FieldsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new FieldsCtrl(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_STEP:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_STEP:
                    case self::CMD_EDIT_STEP:
                    case self::CMD_MOVE_STEP_DOWN:
                    case self::CMD_MOVE_STEP_UP:
                    case self::CMD_REMOVE_STEP:
                    case self::CMD_REMOVE_STEP_CONFIRM:
                    case self::CMD_UPDATE_STEP:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return StepsGUI
     */
    public function getParent() : StepsGUI
    {
        return $this->parent;
    }


    /**
     * @return Step
     */
    public function getStep() : Step
    {
        return $this->step;
    }


    /**
     *
     */
    protected function addStep()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newFormInstance($this, $this->step);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(StepsGUI::class, StepsGUI::CMD_LIST_STEPS);
    }


    /**
     *
     */
    protected function createStep()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newFormInstance($this, $this->step);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_STEP_ID, $this->step->getStepId());

        ilUtil::sendSuccess(self::plugin()->translate("added_step", StepsGUI::LANG_MODULE, [$this->step->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_STEP);
    }


    /**
     *
     */
    protected function editStep()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_STEP);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newFormInstance($this, $this->step);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function moveStepDown()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->steps()->moveStepDown($this->step);

        exit;
    }


    /**
     *
     */
    protected function moveStepUp()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->steps()->moveStepUp($this->step);

        exit;
    }


    /**
     *
     */
    protected function removeStep()/*: void*/
    {
        self::srUserEnrolment()->enrolmentWorkflow()->steps()->deleteStep($this->step);

        ilUtil::sendSuccess(self::plugin()->translate("removed_step", StepsGUI::LANG_MODULE, [$this->step->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeStepConfirm()/*: void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_step_confirm", StepsGUI::LANG_MODULE, [$this->step->getTitle()]));

        $confirmation->addItem(self::GET_PARAM_STEP_ID, $this->step->getStepId(), $this->step->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", StepsGUI::LANG_MODULE), self::CMD_REMOVE_STEP);
        $confirmation->setCancel(self::plugin()->translate("cancel", StepsGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("steps", StepsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if ($this->step !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_STEP_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_STEP, self::plugin()->translate("remove_step", StepsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_STEP_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_STEP, self::plugin()->translate("settings", StepsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_STEP));

                RulesGUI::addTabs(AbstractRule::PARENT_CONTEXT_STEP);

                ActionsGUI::addTabs();

                self::dic()->tabs()->addTab(FieldsCtrl::TAB_LIST_FIELDS, self::plugin()->translate("required_data", StepsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTargetByClass(FieldsCtrl::class, FieldsCtrl::CMD_LIST_FIELDS));

                self::dic()->locator()->addItem($this->step->getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_STEP));
            }
        } else {
            $this->step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newInstance();

            self::dic()->tabs()->addTab(self::TAB_EDIT_STEP, self::plugin()->translate("add_step", StepsGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_STEP));
        }
    }


    /**
     *
     */
    protected function updateStep()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_STEP);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->steps()->factory()->newFormInstance($this, $this->step);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_step", StepsGUI::LANG_MODULE, [$this->step->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_STEP);
    }
}
