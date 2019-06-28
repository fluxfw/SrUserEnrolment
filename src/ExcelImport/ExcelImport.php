<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilExcel;
use ilObjCourse;
use ilSession;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
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
	 * @return string
	 */
	public function import(ExcelImportFormGUI $form): string {
		$excel = new ilExcel();

		$excel->loadFromFile($form->getExcelFile());

		$rows = $excel->getSheetAsArray();

		$rows = array_slice($rows, $form->getCountSkipTopRows());

		$columns_map = array_filter($form->getMappingFields());
		$columns_map = array_reduce(array_map(function (/*string*/ $field, /*string*/ $column): stdClass {
			return (object)[
				"field" => trim(strval($field)),
				"column" => trim(strval($column))
			];
		}, array_keys($columns_map), $columns_map), function (array $columns_map, stdClass $field): array {
			if (!isset($columns_map[$field->column])) {
				$columns_map[$field->column] = [];
			}

			$columns_map[$field->column][] = $field->field;

			return $columns_map;
		}, []);

		$head = array_shift($rows);
		$columns = array_map(function (/*string*/ $column) use (&$columns_map): array {
			if (isset($columns_map[$column])) {
				return $columns_map[$column];
			} else {
				return [];
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

			$has_user_data = false;
			foreach ($row as $cellI => $cell) {
				foreach ($columns[$cellI] as $field) {
					$value = trim(strval($cell));
					if (!empty($value)) {
						$has_user_data = true;
						$user->{$field} = $value;
					}
				}
			}

			if ($has_user_data) {
				$users[] = $user;
			}
		}

		$users = array_map(function (stdClass $user) use ($form): stdClass {
			switch ($user->gender) {
				case $form->getGenderM():
					$user->gender = "m";
					break;

				case $form->getGenderF():
					$user->gender = "f";
					break;

				case $form->getGenderN():
					$user->gender = "n";
					break;

				default:
					$user->gender = "";
					break;
			}

			return $user;
		}, $users);

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

		$object = new ilObjCourse(self::rules()->getObjId(), false);

		$exists_users = array_filter($exists_users, function (stdClass $user) use ($object): bool {
			return (!self::ilias()->courses()->isAssigned($object, $user->ilias_user_id));
		});

		if ($form->isCreateNewUsers()) {
			$new_users = array_filter($users, function (stdClass $user): bool {
				return empty($user->ilias_user_id);
			});
		} else {
			$new_users = [];
		}

		$data = (object)[
			"new_users" => $new_users,
			"exists_users" => $exists_users
		];

		ilSession::set(self::SESSION_KEY, json_encode($data));

		$users = array_merge(array_map(function (stdClass $user): string {
			unset($user->ilias_user_id);

			$items = [];
			foreach ($user as $key => $value) {
				$items[self::plugin()->translate($key, ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT)] = $value;
			}

			return self::output()->getHTML([
				self::plugin()->translate("create_user_and_enroll", ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT),
				self::dic()->ui()->factory()->listing()->descriptive($items)
			]);
		}, $new_users), array_map(function (stdClass $user): string {
			unset($user->ilias_user_id);

			$items = [];
			foreach ($user as $key => $value) {
				$items[self::plugin()->translate($key, ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT)] = $value;
			}

			return self::output()->getHTML([
				self::plugin()->translate("enroll", ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT),
				self::dic()->ui()->factory()->listing()->descriptive($items)
			]);
		}, $exists_users));

		return implode("<br>", $users);
	}


	/**
	 * @return string
	 */
	public function enroll(): string {
		$data = (object)json_decode(ilSession::get(self::SESSION_KEY));
		$new_users = (array)$data->new_users;
		$exists_users = (array)$data->exists_users;

		$object = new ilObjCourse(self::rules()->getObjId(), false);

		if (count($new_users) > 0) {
			foreach ($new_users as &$user) {
				try {
					$user->ilias_user_id = self::ilias()->users()
						->createNewAccount(strval($user->login), strval($user->email), strval($user->first_name), strval($user->last_name), strval($user->gender));
				} catch (Throwable $ex) {
					self::logs()->storeLog(self::logs()->factory()->exceptionLog($ex, $object->getId(), 0));

					continue;
				}

				self::logs()->storeLog(self::logs()->factory()->objectRuleUserLog($object->getId(), 0, $user->ilias_user_id)
					->withStatus(Log::STATUS_USER_CREATED)->withMessage("User data: " . json_encode($user)));

				$exists_users[] = $user;
			}
		}

		foreach ($exists_users as $user) {
			try {
				self::ilias()->courses()->enrollMemberToCourse($object, $user->ilias_user_id, $user->first_name . " " . $user->last_name);
			} catch (Throwable $ex) {
				self::logs()->storeLog(self::logs()->factory()->exceptionLog($ex, $object->getId(), 0));

				continue;
			}

			self::logs()->storeLog(self::logs()->factory()->objectRuleUserLog($object->getId(), 0, $user->ilias_user_id)
				->withStatus(Log::STATUS_ENROLLED));
		}

		ilSession::clear(self::SESSION_KEY);

		$logs = array_reduce(Log::$statuss, function (array $logs, int $status): array {
			$logs[] = self::plugin()->translate("status_" . $status, LogsGUI::LANG_MODULE_LOGS) . ": " . count(self::logs()->getKeptLogs($status));

			return $logs;
		}, []);

		return implode("<br>", $logs);
	}
}
