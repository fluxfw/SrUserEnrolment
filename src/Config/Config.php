<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\ActiveRecordConfig;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Config
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Config extends ActiveRecordConfig {

	use SrUserEnrolmentTrait;
	const TABLE_NAME = "srusrenr_config";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const KEY_ROLES = "roles";
	const KEY_SHOW_EXCEL_IMPORT = "show_excel_import";
	const KEY_SHOW_RESET_PASSWORD = "show_reset_password";
	const KEY_SHOW_RULES_ENROLL = "show_rules_enroll";
	/**
	 * @var array
	 */
	protected static $fields = [
		self::KEY_ROLES => [ self::TYPE_JSON, [] ],
		self::KEY_SHOW_EXCEL_IMPORT => [ self::TYPE_BOOLEAN, false ],
		self::KEY_SHOW_RESET_PASSWORD => [ self::TYPE_BOOLEAN, false ],
		self::KEY_SHOW_RULES_ENROLL => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_COUNT_SKIP_TOP_ROWS => [ self::TYPE_INTEGER, 1 ],
		ExcelImportFormGUI::KEY_CREATE_NEW_USERS => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_EMAIL => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_EMAIL . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_FIRST_NAME => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_FIRST_NAME . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_GENDER => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_GENDER . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_GENDER_F => [ self::TYPE_STRING, "f" ],
		ExcelImportFormGUI::KEY_FIELD_GENDER_M => [ self::TYPE_STRING, "m" ],
		ExcelImportFormGUI::KEY_FIELD_GENDER_N => [ self::TYPE_STRING, "n" ],
		ExcelImportFormGUI::KEY_FIELD_LAST_NAME => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_LAST_NAME . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_LOCAL_USER_ADMINISTRATION_LOCATION => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_LOGIN => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_LOGIN . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_FIELD_PASSWORD => [ self::TYPE_STRING, "" ],
		ExcelImportFormGUI::KEY_FIELD_PASSWORD . ExcelImportFormGUI::UPDATE_SUFFIX => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION => [ self::TYPE_BOOLEAN, false ],
		ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE => [
			self::TYPE_INTEGER,
			ExcelImportFormGUI::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY
		],
		ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION_TYPE => [ self::TYPE_INTEGER, ExcelImportFormGUI::LOCAL_USER_ADMINISTRATION_TYPE_TITLE ],
		ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD => [ self::TYPE_STRING, ExcelImportFormGUI::KEY_FIELD_LOGIN ],
		ExcelImportFormGUI::KEY_SET_PASSWORD => [ self::TYPE_INTEGER, ExcelImportFormGUI::SET_PASSWORD_RANDOM ]
	];


	/**
	 * @inheritDoc
	 */
	protected static function getDefaultValue(/*string*/ $name, /*int*/ $type, $default_value) {
		switch ($name) {
			case ExcelImportFormGUI::KEY_FIELD_EMAIL:
			case ExcelImportFormGUI::KEY_FIELD_FIRST_NAME:
			case ExcelImportFormGUI::KEY_FIELD_GENDER:
			case ExcelImportFormGUI::KEY_FIELD_LAST_NAME:
			case ExcelImportFormGUI::KEY_FIELD_LOCAL_USER_ADMINISTRATION_LOCATION:
			case ExcelImportFormGUI::KEY_FIELD_LOGIN:
			case ExcelImportFormGUI::KEY_FIELD_PASSWORD:
				return self::plugin()->translate($name);

			default:
				return $default_value;
		}
	}
}
