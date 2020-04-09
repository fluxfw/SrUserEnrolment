<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilLink;
use ilObjCourse;
use ilObjectFactory;
use ilSrUserEnrolmentPlugin;
use ilSubmitButton;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class MembersGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\UsersMembersAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI
 */
class MembersGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_ENROLL_USERS = "enrollUsers";
    const CMD_LIST_MEMBERS = "listMembers";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "members";
    const TAB_MEMBERS = "members";
    /**
     * @var int
     */
    protected $obj_ref_id;


    /**
     * MembersGUI constructor
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

        if (!self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(MemberGUI::class):
                self::dic()->ctrl()->forwardCommand(new MemberGUI($this));
                break;

            case strtolower(UsersMembersAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UsersMembersAjaxAutoCompleteCtrl($this));
                break;
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_ENROLL_USERS:
                    case self::CMD_LIST_MEMBERS:
                        $this->{$cmd}();
                        break;

                    default:

                        foreach (Member::TYPES as $type => $type_lang_key) {
                            if ($type === Member::TYPE_REQUEST) {
                                continue;
                            }

                            if ($cmd === self::CMD_ENROLL_USERS . ucfirst($type_lang_key)) {
                                $this->{$cmd}();
                                break;
                            }
                        }
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()
                ->tabs()
                ->addTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function redirect(int $obj_ref_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->ctrl()->redirectByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], MembersGUI::CMD_LIST_MEMBERS);
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()
            ->tabs()
            ->addTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));

        SelectWorkflowGUI::addTabs($this->obj_ref_id);

        RequestsGUI::addTabs($this->obj_ref_id);

        self::dic()
            ->tabs()
            ->addSubTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));

        RulesCourseGUI::addTabs($this->obj_ref_id);

        ExcelImportGUI::addTabs($this->obj_ref_id);
    }


    /**
     *
     */
    protected function back()/*:void*/
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->obj_ref_id));
    }


    /**
     *
     */
    protected function listMembers()/*:void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_MEMBERS);
        self::dic()->tabs()->activateSubTab(self::TAB_MEMBERS);

        $obj = ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);
        if ($obj instanceof ilObjCourse) {
            self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getFormAction($this));

            $users = new MultiSelectSearchNewInputGUI("", MemberGUI::GET_PARAM_USER_ID);
            $users->setAjaxAutoCompleteCtrl(new UsersMembersAjaxAutoCompleteCtrl($this));
            self::dic()->toolbar()->addInputItem($users);

            foreach (Member::TYPES as $type => $type_lang_key) {
                if ($type === Member::TYPE_REQUEST) {
                    continue;
                }

                $enroll_users_button = ilSubmitButton::getInstance();
                $enroll_users_button->setCaption(self::plugin()->translate("enroll_users_as", self::LANG_MODULE, [
                    self::plugin()->translate("member_type_" . $type_lang_key, self::LANG_MODULE)
                ]), false);
                $enroll_users_button->setCommand(self::CMD_ENROLL_USERS . ucfirst($type_lang_key));
                self::dic()->toolbar()->addButtonInstance($enroll_users_button);
            }
        }

        $table = self::srUserEnrolment()->enrolmentWorkflow()->members()->factory()->newTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     * @param int $type
     */
    protected function enrollUsers(int $type)/*:void*/
    {
        $obj = ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);

        if ($obj instanceof ilObjCourse) {
            $user_ids = filter_input(INPUT_POST, MemberGUI::GET_PARAM_USER_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (!is_array($user_ids)) {
                $user_ids = [];
            }

            foreach ($user_ids as $user_id) {
                if (!$obj->getMembersObject()->isAssigned($user_id)) {
                    $obj->getMembersObject()->add($user_id, $type);
                }
            }
        }

        ilUtil::sendSuccess(self::plugin()->translate("enrolled_users", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_MEMBERS);
    }


    /**
     *
     */
    protected function enrollUsersAdmin()/*:void*/
    {
        $this->enrollUsers(IL_CRS_ADMIN);
    }


    /**
     *
     */
    protected function enrollUsersTutor()/*:void*/
    {
        $this->enrollUsers(IL_CRS_TUTOR);
    }


    /**
     *
     */
    protected function enrollUsersMember()/*:void*/
    {
        $this->enrollUsers(IL_CRS_MEMBER);
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }
}
