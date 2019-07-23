<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilCheckboxInputGUI;
use ilMultiSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\ActiveRecordConfigFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ConfigFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigFormGUI extends ActiveRecordConfigFormGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const CONFIG_CLASS_NAME = Config::class;


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/ $key) {
		switch ($key) {
			default:
				return parent::getValue($key);
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			Config::KEY_ROLES => [
				self::PROPERTY_CLASS => ilMultiSelectInputGUI::class,
				self::PROPERTY_REQUIRED => true,
				self::PROPERTY_OPTIONS => self::ilias()->roles()->getAllRoles(),
				"enableSelectAll" => true
			],
			Config::KEY_SHOW_RULES_ENROLL => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			],
			Config::KEY_SHOW_EXCEL_IMPORT => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
				self::PROPERTY_SUBITEMS => ExcelImportFormGUI::getExcelImportFields()
			],
			Config::KEY_SHOW_RESET_PASSWORD => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			]
		];
	}


	/**
	 * @inheritDoc
	 */
	public function storeForm(): bool {
		return ($this->storeFormCheck() && ($this->getInput(Config::KEY_SHOW_EXCEL_IMPORT) ? ExcelImportFormGUI::validateExcelImport($this) : true)
			&& parent::storeForm());
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/ $key, $value)/*: void*/ {
		switch ($key) {
			case Config::KEY_ROLES:
				if ($value[0] === "") {
					array_shift($value);
				}

				$value = array_map(function (string $role_id): int {
					return intval($role_id);
				}, $value);
				break;

			case ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint":
				return;

			default:
				break;
		}

		parent::storeValue($key, $value);
	}
}
