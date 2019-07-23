<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilDBConstants;
use ilExcel;
use ilObjCourse;
use ilSession;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
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
	const FIELDS_TYPE_ILIAS = 1;
	const FIELDS_TYPE_CUSTOM = 2;
	const LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY = 1;
	const LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT = 2;
	const LOCAL_USER_ADMINISTRATION_TYPE_TITLE = 1;
	const LOCAL_USER_ADMINISTRATION_TYPE_REF_ID = 2;
	const MAP_EXISTS_USERS_LOGIN = 1;
	const MAP_EXISTS_USERS_EMAIL = 2;
	const ORG_UNIT_POSITION_FIELD = 0;
	const ORG_UNIT_TYPE_TITLE = 1;
	const ORG_UNIT_TYPE_REF_ID = 2;
	const SET_PASSWORD_RANDOM = 1;
	const SET_PASSWORD_FIELD = 2;


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

		$fields = array_filter(array_map(function (array $field): stdClass {
			$field = (object)$field;

			$field->type = intval($field->type);
			$field->key = trim($field->key);
			$field->column_heading = trim($field->column_heading);
			$field->update = boolval($field->update);

			return $field;
		}, $form->getUserFields()), function (stdClass $field): bool {
			return (!empty($field->type) && !empty($field->key) && !empty($field->column_heading));
		});

		$columns_map = array_reduce($fields, function (array $columns_map, stdClass $field): array {
			if (!isset($columns_map[$field->column_heading])) {
				$columns_map[$field->column_heading] = [];
			}

			$columns_map[$field->column_heading][] = $field;

			return $columns_map;
		}, []);

		$head = array_shift($rows);
		$columns = array_map(function (/*string*/ $column) use ($columns_map): array {
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
				"is_new" => false,
				ExcelImportFormGUI::KEY_FIELDS => [
					self::FIELDS_TYPE_ILIAS => [],
					self::FIELDS_TYPE_CUSTOM => []
				]
			];

			$has_user_data = false;
			foreach ($row as $cellI => $cell) {
				foreach ($columns[$cellI] as $field) {
					$value = trim(strval($cell));
					if (!empty($value)) {
						$has_user_data = true;
						$user->{ExcelImportFormGUI::KEY_FIELDS}[$field->type][$field->key] = $value;
					}
				}
			}

			if ($has_user_data) {
				$user->{ExcelImportFormGUI::KEY_FIELDS} = json_decode(json_encode($user->{ExcelImportFormGUI::KEY_FIELDS}));
				$users[] = $user;
			}
		}

		$users = array_map(function (stdClass $user) use ($form): stdClass {
			switch ($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender) {
				case $form->getGenderM():
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "m";
					break;

				case $form->getGenderF():
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "f";
					break;

				case $form->getGenderN():
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "n";
					break;

				default:
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "";
					break;
			}

			if ($form->getSetPassword() !== self::SET_PASSWORD_FIELD) {
				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->password = null;
			}

			if (self::ilias()->users()->isLocalUserAdminisrationEnabled() && $form->isLocalUserAdministration()) {
				$value = $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner;

				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = null;

				if (!empty($value)) {
					switch ($form->getLocalUserAdministrationObjectType()) {
						case self::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY:
							$obj_type = "cat";
							break;

						case self::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT:
							$obj_type = "orgu";
							break;

						default:
							$obj_type = "";
							break;
					}

					if (!empty($obj_type)) {
						$wheres = [ 'type=%s' ];
						$types = [ ilDBConstants::T_TEXT ];
						$values = [ $obj_type ];

						switch ($form->getLocalUserAdministrationType()) {
							case self::LOCAL_USER_ADMINISTRATION_TYPE_TITLE:
								$wheres[] = self::dic()->database()->like("title", ilDBConstants::T_TEXT, '%' . $value . '%');
								break;

							case self::LOCAL_USER_ADMINISTRATION_TYPE_REF_ID:
								$wheres[] = "ref_id=%s";
								$types[] = ilDBConstants::T_INTEGER;
								$values[] = $value;
								break;

							default:
								break;
						}

						$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = self::ilias()
							->getObjectRefIdByFilter($wheres, $types, $values);
					}
				}
			} else {
				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = null;
			}

			if ($form->isOrgUnitAssign()) {
				$value = $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit;

				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = null;

				if (!empty($value)) {
					$wheres = [ 'type=%s' ];
					$types = [ ilDBConstants::T_TEXT ];
					$values = [ "orgu" ];

					switch ($form->getOrgUnitType()) {
						case self::ORG_UNIT_TYPE_TITLE:
							$wheres[] = self::dic()->database()->like("title", ilDBConstants::T_TEXT, '%' . $value . '%');
							break;

						case self::ORG_UNIT_TYPE_REF_ID:
							$wheres[] = "ref_id=%s";
							$types[] = ilDBConstants::T_INTEGER;
							$values[] = $value;
							break;

						default:
							break;
					}

					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = self::ilias()
						->getObjectRefIdByFilter($wheres, $types, $values);
				}

				if ($form->getOrgUnitPosition() === self::ORG_UNIT_POSITION_FIELD) {
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = self::ilias()->orgUnits()
						->getPositionIdByTitle($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position);
				} else {
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = $form->getOrgUnitPosition();
				}
			} else {
				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = null;
				$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = null;
			}

			return $user;
		}, $users);

		$exists_users = array_filter($users, function (stdClass &$user) use ($form): bool {
			switch ($form->getMapExistsUsersField()) {
				case self::MAP_EXISTS_USERS_LOGIN:
					if (!empty($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->login)) {
						$user->ilias_user_id = self::ilias()->users()
							->getUserIdByLogin(strval($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->login));
					}
					break;

				case self::MAP_EXISTS_USERS_EMAIL:
					if (!empty($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->email)) {
						$user->ilias_user_id = self::ilias()->users()
							->getUserIdByEmail(strval($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->email));
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
			$new_users = array_filter($users, function (stdClass &$user): bool {

				if (empty($user->ilias_user_id)) {
					$user->is_new = true;

					return true;
				} else {
					return false;
				}
			});
		} else {
			$new_users = [];
		}

		$users = array_merge($new_users, $exists_users);

		$data = (object)[
			"users" => $users,
			"fields" => $fields
		];

		ilSession::set(self::SESSION_KEY, json_encode($data));

		$users = array_map(function (stdClass $user): string {
			$items = [];
			foreach ($user->{ExcelImportFormGUI::KEY_FIELDS} as $type => $fields) {
				foreach ($fields as $key => $value) {
					$items[ExcelImportFormGUI::fieldName($type, $key)] = strval($value);
				}
			}

			return self::output()->getHTML([
				self::plugin()
					->translate($user->is_new ? "create_user_and_enroll" : "update_user_and_enroll", ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT),
				":",
				self::dic()->ui()->factory()->listing()->descriptive($items)
			]);
		}, $users);

		return implode("<br>", $users);
	}


	/**
	 * @return string
	 */
	public function enroll(): string {
		$data = (object)json_decode(ilSession::get(self::SESSION_KEY));
		$users = (array)$data->users;
		$fields = (array)$data->fields;
		$update_fields = array_reduce($fields, function (array $fields, stdClass $field): array {
			if ($field->update) {
				$fields[$field->type][$field->key] = true;
			}

			return $fields;
		}, [
			self::FIELDS_TYPE_ILIAS => [],
			self::FIELDS_TYPE_CUSTOM => []
		]);

		$object = new ilObjCourse(self::rules()->getObjId(), false);

		foreach ($users as &$user) {
			try {
				if ($user->is_new) {
					$user->ilias_user_id = self::ilias()->users()->createNewAccount((array)$user->{ExcelImportFormGUI::KEY_FIELDS});

					self::logs()->storeLog(self::logs()->factory()->objectRuleUserLog($object->getId(), Rule::NO_RULE_ID, $user->ilias_user_id)
						->withStatus(Log::STATUS_USER_CREATED)->withMessage("User data: " . json_encode($user)));
				} else {
					$fields = $user->{ExcelImportFormGUI::KEY_FIELDS};
					foreach ($fields as $type => &$fields_) {
						foreach ($fields_ as $key => $value) {
							if (!isset($update_fields[$type][$key])) {
								unset($fields_->{$key});
							}
						}
					}
					if (self::ilias()->users()->updateUserAccount($user->ilias_user_id, (array)$fields)) {
						self::logs()->storeLog(self::logs()->factory()->objectRuleUserLog($object->getId(), Rule::NO_RULE_ID, $user->ilias_user_id)
							->withStatus(Log::STATUS_USER_UPDATED)->withMessage("User data: " . json_encode($user)));
					}
				}

				if ($user->is_new || isset($update_fields[self::FIELDS_TYPE_ILIAS . "_password"])) {
					$user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->password = self::ilias()->users()
						->resetPassword($user->ilias_user_id, $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->password);
				}

				if ($user->is_new
					|| (isset($update_fields[self::FIELDS_TYPE_ILIAS . "_org_unit"])
						&& isset($update_fields[self::FIELDS_TYPE_ILIAS . "_org_unit_position"]))) {
					self::ilias()->orgUnits()
						->assignOrgUnit($user->ilias_user_id, $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit, $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position);
				}

				self::ilias()->courses()
					->enrollMemberToCourse($object, $user->ilias_user_id, $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->firstname
						. " " . $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->lastname);

				self::logs()->storeLog(self::logs()->factory()->objectRuleUserLog($object->getId(), Rule::NO_RULE_ID, $user->ilias_user_id)
					->withStatus(Log::STATUS_ENROLLED));
			} catch (Throwable $ex) {
				self::logs()->storeLog(self::logs()->factory()->exceptionLog($ex, $object->getId(), Rule::NO_RULE_ID));
			}
		}

		ilSession::clear(self::SESSION_KEY);

		$logs = array_reduce(Log::$statuss, function (array $logs, int $status): array {
			$logs[] = self::plugin()->translate("status_" . $status, LogsGUI::LANG_MODULE_LOGS) . ": " . count(self::logs()->getKeptLogs($status));

			return $logs;
		}, []);

		return implode("<br>", $logs);
	}
}
