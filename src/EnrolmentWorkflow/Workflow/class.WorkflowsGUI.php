<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class WorkflowsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI: ilSrUserEnrolmentConfigGUI
 */
class WorkflowsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_DISABLE_WORKFLOWS = "disableWorkflows";
    const CMD_ENABLE_WORKFLOWS = "enableWorkflows";
    const CMD_LIST_WORKFLOWS = "listWorkflows";
    const CMD_REMOVE_WORKFLOWS = "removeWorkflows";
    const CMD_REMOVE_WORKFLOWS_CONFIRM = "removeWorkflowsConfirm";
    const LANG_MODULE = "workflows";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_LIST_WORKFLOWS = "list_workflows";


    /**
     * WorkflowsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public static function addTabs()/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId(), false)) {
            self::dic()->tabs()->addTab(self::TAB_LIST_WORKFLOWS, self::plugin()->translate("workflows", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass(self::class, self::CMD_LIST_WORKFLOWS));

            self::dic()->tabs()->addTab(NotificationsCtrl::TAB_NOTIFICATIONS, self::plugin()->translate("notifications", NotificationsCtrl::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass(NotificationsCtrl::class, NotificationsCtrl::CMD_LIST_NOTIFICATIONS));
        }
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId(), false)) {
            die();
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(WorkflowGUI::class):
                self::dic()->ctrl()->forwardCommand(new WorkflowGUI());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_DISABLE_WORKFLOWS:
                    case self::CMD_ENABLE_WORKFLOWS:
                    case self::CMD_LIST_WORKFLOWS:
                    case self::CMD_REMOVE_WORKFLOWS:
                    case self::CMD_REMOVE_WORKFLOWS_CONFIRM:
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
    protected function disableWorkflows()/*: void*/
    {
        $workflow_ids = filter_input(INPUT_POST, WorkflowGUI::GET_PARAM_WORKFLOW_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($workflow_ids)) {
            $workflow_ids = [];
        }

        /**
         * @var Workflow[] $workflows
         */
        $workflows = array_map(function (int $workflow_id) : Workflow {
            return self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($workflow_id);
        }, $workflow_ids);

        foreach ($workflows as $workflow) {
            $workflow->setEnabled(false);

            $workflow->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("disabled_workflows", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_WORKFLOWS);
    }


    /**
     *
     */
    protected function enableWorkflows()/*: void*/
    {
        $workflow_ids = filter_input(INPUT_POST, WorkflowGUI::GET_PARAM_WORKFLOW_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($workflow_ids)) {
            $workflow_ids = [];
        }

        /**
         * @var Workflow[] $workflows
         */
        $workflows = array_map(function (int $workflow_id) : Workflow {
            return self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($workflow_id);
        }, $workflow_ids);

        foreach ($workflows as $workflow) {
            $workflow->setEnabled(true);

            $workflow->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("enabled_workflows", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_WORKFLOWS);
    }


    /**
     *
     */
    protected function listWorkflows()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_WORKFLOWS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->factory()->newTableInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function removeWorkflows()/*: void*/
    {
        $workflow_ids = filter_input(INPUT_POST, WorkflowGUI::GET_PARAM_WORKFLOW_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($workflow_ids)) {
            $workflow_ids = [];
        }

        /**
         * @var Workflow[] $workflows
         */
        $workflows = array_map(function (int $workflow_id) : Workflow {
            return self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($workflow_id);
        }, $workflow_ids);

        foreach ($workflows as $workflow) {
            self::srUserEnrolment()->enrolmentWorkflow()->workflows()->deleteWorkflow($workflow);
        }

        ilUtil::sendSuccess(self::plugin()->translate("removed_workflows", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_WORKFLOWS);
    }


    /**
     *
     */
    protected function removeWorkflowsConfirm()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_WORKFLOWS);

        $workflow_ids = filter_input(INPUT_POST, WorkflowGUI::GET_PARAM_WORKFLOW_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($workflow_ids)) {
            $workflow_ids = [];
        }

        /**
         * @var Workflow[] $workflows
         */
        $workflows = array_map(function (int $workflow_id) : Workflow {
            return self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($workflow_id);
        }, $workflow_ids);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_workflows_confirm", self::LANG_MODULE));

        foreach ($workflows as $workflow) {
            $confirmation->addItem(WorkflowGUI::GET_PARAM_WORKFLOW_ID . "[]", $workflow->getWorkflowId(), $workflow->getTitle());
        }

        $confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE), self::CMD_REMOVE_WORKFLOWS);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_LIST_WORKFLOWS);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
