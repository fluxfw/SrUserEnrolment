<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilConfirmationGUI;
use ilObjCourse;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\CheckboxInputGUI\AjaxCheckbox;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class MemberGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MemberGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI
 */
class MemberGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_BACK = "back";
    const CMD_EDIT_MEMBER = "editMember";
    const CMD_REMOVE_MEMBER = "removeMember";
    const CMD_REMOVE_MEMBER_CONFIRM = "removeMemberConfirm";
    const CMD_SET_COMPLETED = "setCompleted";
    const CMD_SET_CUSTOM_CHECKED = "setCustomChecked";
    const CMD_UPDATE_MEMBER = "updateMember";
    const GET_PARAM_CUSTOM_CHECKED = "custom_checked";
    const GET_PARAM_USER_ID = "user_id";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_EDIT_MEMBER = "edit_member";
    /**
     * @var Member
     */
    protected $member;
    /**
     * @var MembersGUI
     */
    protected $parent;


    /**
     * MemberGUI constructor
     *
     * @param MembersGUI $parent
     */
    public function __construct(MembersGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->member = self::srUserEnrolment()->enrolmentWorkflow()->members()->getMember($this->parent->getObjRefId(), intval(filter_input(INPUT_GET, self::GET_PARAM_USER_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_USER_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_EDIT_MEMBER:
                    case self::CMD_REMOVE_MEMBER:
                    case self::CMD_REMOVE_MEMBER_CONFIRM:
                    case self::CMD_SET_COMPLETED:
                    case self::CMD_SET_CUSTOM_CHECKED:
                    case self::CMD_UPDATE_MEMBER:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return Member
     */
    public function getMember() : Member
    {
        return $this->member;
    }


    /**
     *
     */
    protected function back()/*:void*/
    {
        self::dic()->ctrl()->redirectByClass(MembersGUI::class, MembersGUI::CMD_LIST_MEMBERS);
    }


    /**
     *
     */
    protected function editMember()/*: void*/
    {
        if ($this->member->getType() === Member::TYPE_REQUEST) {
            die();
        }

        self::dic()->tabs()->activateTab(self::TAB_EDIT_MEMBER);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->members()->factory()->newFormInstance($this, $this->member);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function removeMember()/*: void*/
    {
        if ($this->member->getType() === Member::TYPE_REQUEST) {
            die();
        }

        if ($this->member->getObject() instanceof ilObjCourse) {
            self::srUserEnrolment()->ruleEnrolment()->unenroll($this->member->getObjId(), $this->member->getUsrId());
        }

        ilUtil::sendSuccess(self::plugin()->translate("removed_member", MembersGUI::LANG_MODULE, [$this->member->getUser()->getFullname()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeMemberConfirm()/*: void*/
    {
        if ($this->member->getType() === Member::TYPE_REQUEST) {
            die();
        }

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_member_confirm", MembersGUI::LANG_MODULE, [$this->member->getUser()->getFullname()]));

        $confirmation->addItem(self::GET_PARAM_USER_ID, $this->member->getUsrId(), $this->member->getUser()->getFullname());

        $confirmation->setConfirm(self::plugin()->translate("remove", MembersGUI::LANG_MODULE), self::CMD_REMOVE_MEMBER);
        $confirmation->setCancel(self::plugin()->translate("cancel", MembersGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function setCompleted()/*:void*/
    {
        $completed = (filter_input(INPUT_POST, AjaxCheckbox::GET_PARAM_CHECKED) === "true");

        $this->member->setLpCompleted($completed);

        exit;
    }


    /**
     *
     */
    protected function setCustomChecked()/*:void*/
    {
        $key = strval(filter_input(INPUT_GET, self::CMD_SET_CUSTOM_CHECKED));
        $checked = (filter_input(INPUT_POST, AjaxCheckbox::GET_PARAM_CHECKED) === "true");

        $this->member->setAdditionalDataValueCustomChecked($key, $checked);

        self::srUserEnrolment()->enrolmentWorkflow()->members()->storeMember($this->member);

        exit;
    }


    /**
     *
     */
    protected function setTabs()/*:void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("members", MembersGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_MEMBER_CONFIRM) {
            self::dic()->tabs()->addTab(self::TAB_EDIT_MEMBER, self::plugin()->translate("remove_member", MembersGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_REMOVE_MEMBER_CONFIRM));
        } else {
            self::dic()->tabs()->addTab(self::TAB_EDIT_MEMBER, self::plugin()->translate("edit_member", MembersGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_EDIT_MEMBER));
        }
    }


    /**
     *
     */
    protected function updateMember()/*: void*/
    {
        if ($this->member->getType() === Member::TYPE_REQUEST) {
            die();
        }

        self::dic()->tabs()->activateTab(self::TAB_EDIT_MEMBER);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->members()->factory()->newFormInstance($this, $this->member);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_member", MembersGUI::LANG_MODULE, [$this->member->getUser()->getFullname()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_MEMBER);
    }
}
