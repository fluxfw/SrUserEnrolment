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
class ResetPasswordGUI {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const CMD_RESET_PASSWORD_CONFIRM = "resetPasswordConfirm";
	const CMD_RESET_PASSWORD = "resetPassword";
	const CMD_BACK_TO_MEMBERS_LIST = "backToMembersList";
	const LANG_MODULE_RESET_PASSWORD = "reset_password";


	/**
	 * ResetPasswordGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->currentUserHasRole()
			|| !self::dic()->access()->checkAccess("write", "", self::rules()->getRefId()
				|| !self::ilias()->courses()->isMember(new ilObjCourse(self::rules()->getObjId(), false), self::rules()->getUserId()))) {
			die();
		}

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
	protected function resetPasswordConfirm()/*: void*/ {
		$user = new ilObjUser(self::rules()->getUserId());

		$confirmation = new ilConfirmationGUI();

		self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);
		self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_USER_ID);

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::plugin()->translate("confirmation", self::LANG_MODULE_RESET_PASSWORD));

		$confirmation->addItem(Repository::GET_PARAM_USER_ID, $user->getId(), $user->getFullname());

		$confirmation->setConfirm(self::plugin()->translate("button", self::LANG_MODULE_RESET_PASSWORD), self::CMD_RESET_PASSWORD);
		$confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_RESET_PASSWORD), self::CMD_BACK_TO_MEMBERS_LIST);

		self::output()->output($confirmation, true);
	}


	/**
	 *
	 */
	protected function resetPassword()/*: void*/ {
		$user = new ilObjUser(self::rules()->getUserId());

		$new_password = self::ilias()->users()->resetPassword($user->getId());

		ilUtil::sendSuccess(nl2br(str_replace("\\n", "\n", self::plugin()->translate("reset", self::LANG_MODULE_RESET_PASSWORD, [
			$user->getFullname(),
			$user->getEmail(),
			$user->getLogin(),
			$new_password
		])), false), true);

		self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);

		self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);
	}


	/**
	 *
	 */
	protected function backToMembersList()/*: void*/ {
		self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, Repository::GET_PARAM_REF_ID);

		self::dic()->ctrl()->redirectByClass([
			ilRepositoryGUI::class,
			ilObjCourseGUI::class,
			ilCourseMembershipGUI::class
		]);
	}
}
