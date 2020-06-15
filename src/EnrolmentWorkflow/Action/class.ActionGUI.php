<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ActionGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionsGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI
 * @ilCtrl_isCalledBy srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI
 */
class ActionGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_ADD_ACTION = "addAction";
    const CMD_BACK = "back";
    const CMD_CREATE_ACTION = "createAction";
    const CMD_EDIT_ACTION = "editAction";
    const CMD_MOVE_ACTION_DOWN = "moveActionDown";
    const CMD_MOVE_ACTION_UP = "moveActionUp";
    const CMD_REMOVE_ACTION = "removeAction";
    const CMD_REMOVE_ACTION_CONFIRM = "removeActionConfirm";
    const CMD_UPDATE_ACTION = "updateAction";
    const GET_PARAM_ACTION_ID = "action_id";
    const GET_PARAM_ACTION_TYPE = "action_type";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_EDIT_ACTION = "edit_action";
    /**
     * @var AbstractAction|null
     */
    protected $action = null;
    /**
     * @var ActionsGUI
     */
    protected $parent;


    /**
     * ActionGUI constructor
     *
     * @param ActionsGUI $parent
     */
    public function __construct(ActionsGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->action = self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActionById(strval(filter_input(INPUT_GET, self::GET_PARAM_ACTION_TYPE)),
            intval(filter_input(INPUT_GET, self::GET_PARAM_ACTION_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_ACTION_TYPE);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_ACTION_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RulesGUI::class):
                self::dic()->ctrl()->forwardCommand(new RulesGUI(AbstractRule::PARENT_CONTEXT_ACTION, $this->action->getId()));
                break;

            case strtolower(UsersAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UsersAjaxAutoCompleteCtrl());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_ACTION:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_ACTION:
                    case self::CMD_EDIT_ACTION:
                    case self::CMD_MOVE_ACTION_DOWN:
                    case self::CMD_MOVE_ACTION_UP:
                    case self::CMD_REMOVE_ACTION:
                    case self::CMD_REMOVE_ACTION_CONFIRM:
                    case self::CMD_UPDATE_ACTION:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return ActionsGUI
     */
    public function getParent() : ActionsGUI
    {
        return $this->parent;
    }


    /**
     *
     */
    protected function addAction()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newCreateFormInstance($this);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(ActionsGUI::class, ActionsGUI::CMD_LIST_ACTIONS);
    }


    /**
     *
     */
    protected function createAction()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newCreateFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        $this->action = $form->getAction();

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_ACTION_TYPE, $this->action->getType());
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_ACTION_ID, $this->action->getActionId());

        ilUtil::sendSuccess(self::plugin()->translate("added_action", ActionsGUI::LANG_MODULE, [$this->action->getActionTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_ACTION);
    }


    /**
     *
     */
    protected function editAction()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_ACTION);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newFormInstance($this, $this->action);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function moveActionDown()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->actions()->moveActionDown($this->action);

        exit;
    }


    /**
     *
     */
    protected function moveActionUp()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->actions()->moveActionUp($this->action);

        exit;
    }


    /**
     *
     */
    protected function removeAction()/*: void*/
    {
        self::srUserEnrolment()->enrolmentWorkflow()->actions()->deleteAction($this->action);

        ilUtil::sendSuccess(self::plugin()->translate("removed_action", ActionsGUI::LANG_MODULE, [$this->action->getActionTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeActionConfirm()/*: void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()
            ->translate("remove_action_confirm", ActionsGUI::LANG_MODULE, [$this->action->getActionTitle()]));

        $confirmation->addItem(self::GET_PARAM_ACTION_ID, $this->action->getId(), $this->action->getActionTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", ActionsGUI::LANG_MODULE), self::CMD_REMOVE_ACTION);
        $confirmation->setCancel(self::plugin()->translate("cancel", ActionsGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("actions", ActionsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if ($this->action !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_ACTION_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_ACTION, self::plugin()->translate("remove_action", ActionsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_ACTION_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_ACTION, self::plugin()->translate("edit_action", ActionsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_ACTION));

                RulesGUI::addTabs(AbstractRule::PARENT_CONTEXT_ACTION);

                self::dic()->locator()->addItem($this->action->getActionTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_ACTION));
            }
        } else {
            self::dic()->tabs()->addTab(self::TAB_EDIT_ACTION, self::plugin()->translate("add_action", ActionsGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_ACTION));
        }
    }


    /**
     *
     */
    protected function updateAction()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_ACTION);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newFormInstance($this, $this->action);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_action", ActionsGUI::LANG_MODULE, [$this->action->getActionTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_ACTION);
    }
}
