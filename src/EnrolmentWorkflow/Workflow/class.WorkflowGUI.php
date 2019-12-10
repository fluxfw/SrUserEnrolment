<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class WorkflowGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI
 */
class WorkflowGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_ADD_WORKFLOW = "addWorkflow";
    const CMD_BACK = "back";
    const CMD_CREATE_WORKFLOW = "createWorkflow";
    const CMD_EDIT_WORKFLOW = "editWorkflow";
    const CMD_REMOVE_WORKFLOW = "removeWorkflow";
    const CMD_REMOVE_WORKFLOW_CONFIRM = "removeWorkflowConfirm";
    const CMD_UPDATE_WORKFLOW = "updateWorkflow";
    const GET_PARAM_WORKFLOW_ID = "workflow_id";
    const TAB_EDIT_WORKFLOW = "edit_workflow";
    /**
     * @var Workflow
     */
    protected $workflow;


    /**
     * WorkflowGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->workflow = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById(intval(filter_input(INPUT_GET, self::GET_PARAM_WORKFLOW_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_WORKFLOW_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(StepsGUI::class);
                self::dic()->ctrl()->forwardCommand(new StepsGUI($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_WORKFLOW:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_WORKFLOW:
                    case self::CMD_EDIT_WORKFLOW:
                    case self::CMD_REMOVE_WORKFLOW:
                    case self::CMD_REMOVE_WORKFLOW_CONFIRM:
                    case self::CMD_UPDATE_WORKFLOW:
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
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if ($this->workflow !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_WORKFLOW_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_WORKFLOW, self::plugin()->translate("remove_workflow", WorkflowsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_WORKFLOW_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_WORKFLOW, self::plugin()->translate("settings", WorkflowsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_WORKFLOW));

                StepsGUI::addTabs();

                self::dic()->locator()->addItem($this->workflow->getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_WORKFLOW));
            }
        } else {
            $this->workflow = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newInstance();

            self::dic()->tabs()->addTab(self::TAB_EDIT_WORKFLOW, self::plugin()->translate("add_workflow", WorkflowsGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_WORKFLOW));
        }
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(WorkflowsGUI::class, WorkflowsGUI::CMD_LIST_WORKFLOWS);
    }


    /**
     *
     */
    protected function addWorkflow()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newFormInstance($this, $this->workflow);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function createWorkflow()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newFormInstance($this, $this->workflow);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_WORKFLOW_ID, $this->workflow->getWorkflowId());

        ilUtil::sendSuccess(self::plugin()->translate("added_workflow", WorkflowsGUI::LANG_MODULE, [$this->workflow->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_WORKFLOW);
    }


    /**
     *
     */
    protected function removeWorkflowConfirm()/*: void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_workflow_confirm", WorkflowsGUI::LANG_MODULE, [$this->workflow->getTitle()]));

        $confirmation->addItem(self::GET_PARAM_WORKFLOW_ID, $this->workflow->getWorkflowId(), $this->workflow->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", WorkflowsGUI::LANG_MODULE), self::CMD_REMOVE_WORKFLOW);
        $confirmation->setCancel(self::plugin()->translate("cancel", WorkflowsGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function removeWorkflow()/*: void*/
    {
        self::srUserEnrolment()->enrolmentWorkflow()->workflows()->deleteWorkflow($this->workflow);

        ilUtil::sendSuccess(self::plugin()->translate("removed_workflow", WorkflowsGUI::LANG_MODULE, [$this->workflow->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function editWorkflow()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_WORKFLOW);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newFormInstance($this, $this->workflow);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function updateWorkflow()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_WORKFLOW);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newFormInstance($this, $this->workflow);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_workflow", WorkflowsGUI::LANG_MODULE, [$this->workflow->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_WORKFLOW);
    }


    /**
     * @return Workflow
     */
    public function getWorkflow() : Workflow
    {
        return $this->workflow;
    }
}
