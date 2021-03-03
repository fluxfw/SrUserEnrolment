<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ActionsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionsGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI
 */
class ActionsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_DISABLE_ACTIONS = "disableActions";
    const CMD_ENABLE_ACTIONS = "enableActions";
    const CMD_LIST_ACTIONS = "listActions";
    const CMD_REMOVE_ACTIONS = "removeActions";
    const CMD_REMOVE_ACTIONS_CONFIRM = "removeActionsConfirm";
    const LANG_MODULE = "actions";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_LIST_ACTIONS = "list_actions";
    /**
     * @var StepGUI
     */
    protected $parent;


    /**
     * ActionsGUI constructor
     *
     * @param StepGUI $parent
     */
    public function __construct(StepGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public static function addTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_ACTIONS, self::plugin()->translate("actions", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_ACTIONS));
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ActionGUI::class):
                self::dic()->ctrl()->forwardCommand(new ActionGUI($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_DISABLE_ACTIONS:
                    case self::CMD_ENABLE_ACTIONS:
                    case self::CMD_LIST_ACTIONS:
                    case self::CMD_REMOVE_ACTIONS:
                    case self::CMD_REMOVE_ACTIONS_CONFIRM:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return StepGUI
     */
    public function getParent() : StepGUI
    {
        return $this->parent;
    }


    /**
     *
     */
    protected function disableActions()/*: void*/
    {
        $action_ids = filter_input(INPUT_POST, ActionGUI::GET_PARAM_ACTION_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($action_ids)) {
            $action_ids = [];
        }

        /**
         * @var AbstractAction[] $actions
         */
        $actions = array_map(function (string $action_id) : AbstractAction {
            list($type, $action_id) = explode("_", $action_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActionById($type, $action_id);
        }, $action_ids);

        foreach ($actions as $action) {
            $action->setEnabled(false);

            $action->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("disabled_actions", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_ACTIONS);
    }


    /**
     *
     */
    protected function enableActions()/*: void*/
    {
        $action_ids = filter_input(INPUT_POST, ActionGUI::GET_PARAM_ACTION_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($action_ids)) {
            $action_ids = [];
        }

        /**
         * @var AbstractAction[] $actions
         */
        $actions = array_map(function (string $action_id) : AbstractAction {
            list($type, $action_id) = explode("_", $action_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActionById($type, $action_id);
        }, $action_ids);

        foreach ($actions as $action) {
            $action->setEnabled(true);

            $action->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("enabled_actions", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_ACTIONS);
    }


    /**
     *
     */
    protected function listActions()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_ACTIONS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newTableInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function removeActions()/*: void*/
    {
        $action_ids = filter_input(INPUT_POST, ActionGUI::GET_PARAM_ACTION_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($action_ids)) {
            $action_ids = [];
        }

        /**
         * @var AbstractAction[] $actions
         */
        $actions = array_map(function (string $action_id) : AbstractAction {
            list($type, $action_id) = explode("_", $action_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActionById($type, $action_id);
        }, $action_ids);

        foreach ($actions as $action) {
            self::srUserEnrolment()->enrolmentWorkflow()->actions()->deleteAction($action);
        }

        ilUtil::sendSuccess(self::plugin()->translate("removed_actions", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_ACTIONS);
    }


    /**
     *
     */
    protected function removeActionsConfirm()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_ACTIONS);

        $action_ids = filter_input(INPUT_POST, ActionGUI::GET_PARAM_ACTION_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($action_ids)) {
            $action_ids = [];
        }

        /**
         * @var AbstractAction[] $actions
         */
        $actions = array_map(function (string $action_id) : AbstractAction {
            list($type, $action_id) = explode("_", $action_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActionById($type, $action_id);
        }, $action_ids);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_actions_confirm", self::LANG_MODULE));

        foreach ($actions as $action) {
            $confirmation->addItem(ActionGUI::GET_PARAM_ACTION_ID . "[]", $action->getId(), $action->getActionTitle());
        }

        $confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE), self::CMD_REMOVE_ACTIONS);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_LIST_ACTIONS);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
