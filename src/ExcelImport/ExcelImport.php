<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilExcel;
use ilObjCourse;
use ilSession;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;
use Throwable;

/**
 * Class ExcelImport
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImport {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const SESSION_KEY = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_excel_import";


	/**
	 * ExcelImport constructor
	 */
	public function __construct() {

	}


	/**
	 * @param ExcelImportFormGUI $form
	 *
	 * @return stdClass
	 */
	public function import(ExcelImportFormGUI $form): stdClass {
		$excel = new ilExcel();

		$excel->loadFromFile($form->getExcelFile());

		$rows = $excel->getSheetAsArray();

		$rows = array_slice($rows, $form->getCountSkipTopRows());

		$columns_map = array_flip(array_filter($form->getMappingFields()));

		$head = array_shift($rows);
		$columns = array_map(function (/*string*/ $column) use (&$columns_map): string {
			if (isset($columns_map[$column])) {
				return strval($columns_map[$column]);
			} else {
				return "";
			}
		}, $head);

		$users = [];

		foreach ($rows as $rowId => $row) {
			$user = (object)[
				"ilias_user_id" => null,
				"login" => "",
				"email" => "",
				"first_name" => "",
				"last_name" => "",
				"gender" => ""
			];

			foreach ($row as $cellI => $cell) {
				if (!empty($columns[$cellI])) {
					$user->{$columns[$cellI]} = strval($cell);
				}
			}

			$users[] = $user;
		}

		$exists_users = array_filter($users, function (stdClass &$user) use ($form): bool {
			switch ($form->getMapExistsUsersField()) {
				case "login":
					if (!empty($user->login)) {
						$user->ilias_user_id = self::ilias()->users()->getUserIdByLogin(strval($user->login));
					}
					break;

				case "email":
					if (!empty($user->email)) {
						$user->ilias_user_id = self::ilias()->users()->getUserIdByEmail(strval($user->email));
					}
					break;

				default:
					break;
			}

			return (!empty($user->ilias_user_id));
		});

		if ($form->isCreateNewUsers()) {
			$new_users = array_filter($users, function (stdClass $user): bool {
				return empty($user->ilias_user_id);
			});
		} else {
			$new_users = [];
		}

		$data = (object)[
			"exists_users" => $exists_users,
			"new_users" => $new_users
		];

		ilSession::set(self::SESSION_KEY, json_encode($data));

		return $data;
	}


	/**
	 * @return stdClass
	 */
	public function enroll(): stdClass {
		$data = (object)json_decode(ilSession::get(self::SESSION_KEY));
		$exists_users = (array)$data->exists_users;
		$new_users = (array)$data->new_users;

		if (count($new_users) > 0) {
			foreach ($new_users as &$user) {
				$user->ilias_user_id = self::ilias()->users()
					->createNewAccount(strval($user->login), strval($user->email), strval($user->first_name), strval($user->last_name), strval($user->gender));

				$exists_users[] = $user;
			}
		}

		$object = new ilObjCourse(self::rules()->getObjId(), false);

		foreach ($exists_users as $user) {
			try {
				self::ilias()->courses()->enrollMemberToCourse($object, $user->ilias_user_id, $user->first_name . " " . $user->last_name);
			} catch (Throwable $ex) {
				self::logs()->storeLog(self::logs()->factory()->exceptionLog($ex, self::rules()->getObjId(), 0));
			}
		}

		ilSession::clear(self::SESSION_KEY);

		$data = (object)[
			"exists_users" => $exists_users,
			"new_users" => $new_users
		];

		return $data;
	}
}
