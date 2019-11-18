<?php

namespace srag\Plugins\SrUserEnrolment\ResetPassword;

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ilObjCourse;
use ilObjCourseGUI;
use ilObjUser;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ResetPasswordGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ResetPassword
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI: ilUIPluginRouterGUI
 */
class ResetPasswordGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_RESET_PASSWORD_CONFIRM = "resetPasswordConfirm";
    const CMD_RESET_PASSWORD = "resetPassword";
    const CMD_BACK_TO_MEMBERS_LIST = "backToMembersList";
    const TAB_RESET_PASSWORD = "reset_password";
    const LANG_MODULE_RESET_PASSWORD = "reset_password";


    /**
     * ResetPasswordGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        if (!Config::getField(Config::KEY_SHOW_RESET_PASSWORD) || !self::access()->currentUserHasRole()
            || !self::ilias()->courses()->isMember(new ilObjCourse(self::rules()->getObjId(), false), self::rules()->getUserId())
        ) {
            die();
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_RESET_PASSWORD_CONFIRM:
                    case self::CMD_RESET_PASSWORD:
                    case self::CMD_BACK_TO_MEMBERS_LIST:
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
        self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_USER_ID);

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", self::LANG_MODULE_RESET_PASSWORD), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK_TO_MEMBERS_LIST));

        self::dic()->tabs()->addTab(self::TAB_RESET_PASSWORD, self::plugin()->translate("title", self::LANG_MODULE_RESET_PASSWORD), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_RESET_PASSWORD_CONFIRM));
        self::dic()->tabs()->activateTab(self::TAB_RESET_PASSWORD);
    }


    /**
     *
     */
    protected function resetPasswordConfirm()/*: void*/
    {
        $user = new ilObjUser(self::rules()->getUserId());

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("confirmation", self::LANG_MODULE_RESET_PASSWORD));

        $confirmation->addItem(Repository::GET_PARAM_USER_ID, $user->getId(), $user->getFullname());

        $confirmation->setConfirm(self::plugin()->translate("title", self::LANG_MODULE_RESET_PASSWORD), self::CMD_RESET_PASSWORD);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_RESET_PASSWORD), self::CMD_BACK_TO_MEMBERS_LIST);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function resetPassword()/*: void*/
    {
        $user = new ilObjUser(self::rules()->getUserId());

        $new_password = self::ilias()->users()->resetPassword($user->getId());

        ilUtil::sendSuccess(nl2br(str_replace("\\n", "\n", self::plugin()->translate("reset", self::LANG_MODULE_RESET_PASSWORD, [
            $user->getFullname(),
            $user->getEmail(),
            $user->getLogin(),
            $new_password
        ])), false), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);
    }


    /**
     *
     */
    protected function backToMembersList()/*: void*/
    {
        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, Repository::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class,
            ilCourseMembershipGUI::class
        ]);
    }
}
