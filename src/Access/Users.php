<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilObjUser;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Users
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Users {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Users constructor
	 */
	private function __construct() {

	}


	/**
	 * @param string $login
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $gender
	 *
	 * @return int
	 */
	public function createNewAccount(string $login, string $email, string $first_name, string $last_name, string $gender): int {
		$user = new ilObjUser();

		$user->setLogin($login);

		$user->setEmail($email);

		$user->setFirstname($first_name);

		$user->setLastname($last_name);

		$user->setGender($gender);

		$user->setActive(true);

		$user->setTimeLimitUnlimited(true);

		$user->create();

		$user->saveAsNew();

		self::dic()->rbacadmin()->assignUser(4, $user->getId()); // User default role

		return $user->getId();
	}


	/**
	 * @return int
	 */
	public function getUserId(): int {
		$user_id = self::dic()->user()->getId();

		// Fix login screen
		if ($user_id === 0 && boolval(self::dic()->settings()->get("pub_section"))) {
			$user_id = ANONYMOUS_USER_ID;
		}

		return intval($user_id);
	}


	/**
	 * @return ilObjUser[]
	 */
	public function getUsers(): array {
		$result = self::dic()->database()->query('SELECT usr_id FROM usr_data');

		$array = [];

		while (($row = $result->fetchAssoc()) !== false) {
			$array[] = new ilObjUser($row["usr_id"]);
		}

		return $array;
	}


	/**
	 * @param string $email
	 *
	 * @return int|null
	 */
	public function getUserIdByEmail(string $email)/*:?int*/ {
		return ilObjUser::_lookupId(current(self::version()
			->is54() ? ilObjUser::getUserLoginsByEmail($email) : ilObjUser::_getUserIdsByEmail($email)));
	}


	/**
	 * @param string $login
	 *
	 * @return int|null
	 */
	public function getUserIdByLogin(string $login)/*:?int*/ {
		return ilObjUser::_lookupId($login);
	}


	/**
	 * @param int         $user_id
	 * @param string|null $new_password
	 *
	 * @return string
	 */
	public function resetPassword(int $user_id, /*?string*/ $new_password = null): string {
		$user = new ilObjUser($user_id);

		if ($new_password === null) {
			$new_password = current(ilUtil::generatePasswords(1));
		}

		$user->resetPassword($new_password, $new_password);

		return $new_password;
	}
}
