<?php

namespace srag\Plugins\SrUserEnrolment\ResetPassword;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ILIAS\UI\Component\Link\Link;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilSrUserEnrolmentUIHookGUI;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ResetPasswordGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ResetPassword
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI: ilUIPluginRouterGUI
 */
class ResetPasswordGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_BACK = "back";
    const CMD_RESET_PASSWORD = "resetPassword";
    const CMD_RESET_PASSWORD_CONFIRM = "resetPasswordConfirm";
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_USER_ID = "user_id";
    const LANG_MODULE = "reset_password";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_RESET_PASSWORD = "reset_password";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var int
     */
    protected $user_id;


    /**
     * ResetPasswordGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param array $a_par
     * @param int   $obj_ref_id
     *
     * @return array
     */
    public static function addActions(array $a_par, int $obj_ref_id) : array
    {
        $html = $a_par["html"];

        $html = preg_replace_callback('/<a class="il_ContainerItemCommand2" href=".+member_id=([0-9]+).+cmd=editMember.+">.+<\/a>/', function (array $matches) use ($obj_ref_id) : string {
            $link = $matches[0];

            $user_id = intval($matches[1]);

            if (self::srUserEnrolment()->resetUserPassword()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $user_id)) {

                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_USER_ID, $user_id);

                $reset_password_link = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::plugin()
                    ->translate("title", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    self::class
                ], self::CMD_RESET_PASSWORD_CONFIRM)));

                $reset_password_link = str_replace('<a ', '<a class="il_ContainerItemCommand2" ', $reset_password_link);

                return self::output()->getHTML([
                    $link,
                    "<br>",
                    $reset_password_link
                ]);
            } else {
                return $link;
            }
        }, $html);

        return ["mode" => ilSrUserEnrolmentUIHookGUI::REPLACE, "html" => $html];
    }


    /**
     * @param int $obj_ref_id
     * @param int $member_id
     *
     * @return Link|null
     */
    public static function getAction(int $obj_ref_id, int $member_id)/* : ?Link*/
    {
        if (self::srUserEnrolment()->resetUserPassword()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $member_id)) {

            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_USER_ID, $member_id);

            return self::dic()->ui()->factory()->link()->standard(self::plugin()
                ->translate("title", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_RESET_PASSWORD_CONFIRM));
        }

        return null;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));
        $this->user_id = intval(filter_input(INPUT_GET, self::GET_PARAM_USER_ID));

        if (!self::srUserEnrolment()->resetUserPassword()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id, $this->user_id)
        ) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_USER_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_RESET_PASSWORD:
                    case self::CMD_RESET_PASSWORD_CONFIRM:
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
    protected function resetPassword()/*: void*/
    {
        $user = self::srUserEnrolment()->getIliasObjectById($this->user_id);

        $new_password = self::srUserEnrolment()->resetUserPassword()->resetPassword($user->getId());

        ilUtil::sendSuccess(nl2br(self::plugin()->translate("reset", self::LANG_MODULE, [
            $user->getFullname(),
            $user->getEmail(),
            $user->getLogin(),
            $new_password
        ]), false), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function resetPasswordConfirm()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_RESET_PASSWORD);

        $user = self::srUserEnrolment()->getIliasObjectById($this->user_id);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("confirmation", self::LANG_MODULE));

        $confirmation->addItem(self::GET_PARAM_USER_ID, $user->getId(), $user->getFullname());

        $confirmation->setConfirm(self::plugin()->translate("title", self::LANG_MODULE), self::CMD_RESET_PASSWORD);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_RESET_PASSWORD, self::plugin()->translate("title", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_RESET_PASSWORD_CONFIRM));
    }
}
