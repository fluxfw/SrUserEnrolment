<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilCheckboxInputGUI;
use ilFileInputGUI;
use ilFormSectionHeaderGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ExcelImportFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImportFormGUI extends PropertyFormGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const LANG_MODULE = ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT;
	/**
	 * @var string
	 */
	protected $excel_file = "";
	/**
	 * @var int
	 */
	protected $count_skip_top_rows = 1;
	/**
	 * @var string
	 */
	protected $map_exists_users_field = "";
	/**
	 * @var bool
	 */
	protected $create_new_users = false;
	/**
	 * @var string[]
	 */
	protected $mapping_fields = [];


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/ $key) {
		switch ($key) {
			case "excel_file":
			case "count_skip_top_rows":
			case "map_exists_users_field":
			case "create_new_users":
				return $this->{$key};

			case "login":
			case "email":
			case "firstname":
			case "lastname":
				return $this->mapping_fields[$key] ?? $this->txt($key);

			default:
				return null;
		}
	}


	/**
	 * @inheritDoc
	 */
	protected function initAction()/*: void*/ {
		self::dic()->ctrl()->saveParameter($this->parent, Repository::GET_PARAM_REF_ID);

		parent::initAction();
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		$this->addCommandButton(ExcelImportGUI::CMD_EXCEL_IMPORT, $this->txt("upload", self::LANG_MODULE));
		$this->addCommandButton(ExcelImportGUI::CMD_BACK_TO_MEMBERS_LIST, $this->txt("cancel", self::LANG_MODULE));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		self::plugin()->getPluginObject()->updateLanguages();
		$this->fields = [
				"excel_file" => [
					self::PROPERTY_CLASS => ilFileInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					"setSuffixes" => [ [ "xlsx" ] ]
				],
				"count_skip_top_rows" => [
					self::PROPERTY_CLASS => ilNumberInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					"setSuffix" => $this->txt("rows")
				],
				"map_exists_users_field" => [
					self::PROPERTY_CLASS => ilRadioGroupInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					self::PROPERTY_SUBITEMS => [
						"login" => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => $this->txt("login")
						],
						"email" => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => $this->txt("email")
						]
					]
				],
				"create_new_users" => [
					self::PROPERTY_CLASS => ilCheckboxInputGUI::class
				],
				"fields_title" => [
					self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class
				]
			] + array_map(function (string $key): array {
				return [
					self::PROPERTY_CLASS => ilTextInputGUI::class
				];
			}, [ "login" => "login", "email" => "email", "firstname" => "firstname", "lastname" => "lastname" ]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle($this->txt("title", self::LANG_MODULE));
	}


	/**
	 * @inheritDoc
	 */
	public function storeForm()/*: bool*/ {
		if (!parent::storeForm()) {
			return false;
		}

		if (empty($this->getMappingFields()[$this->getMapExistsUsersField()])) {
			$this->getItemByPostVar($this->getMapExistsUsersField())->setAlert($this->txt("missing_field_for_map_exists_users"));

			return false;
		}

		if ($this->isCreateNewUsers()) {
			$error = false;
			foreach ([ "login", "email", "firstname", "lastname" ] as $key) {
				if (empty($this->getMappingFields()[$key])) {
					$this->getItemByPostVar($key)->setAlert($this->txt("missing_field_for_create_new_users"));

					$error = true;
				}
			}
			if ($error) {
				return false;
			}
		}

		return true;
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/ $key, $value)/*: void*/ {
		switch ($key) {
			case "excel_file":
				$this->excel_file = strval($this->getInput("excel_file")["tmp_name"]);
				break;

			case "count_skip_top_rows":
			case "map_exists_users_field":
			case "create_new_users":
				$this->{$key} = $value;
				break;

			case "login":
			case "email":
			case "firstname":
			case "lastname":
				$this->mapping_fields[$key] = $value;
				break;

			default:
				break;
		}
	}


	/**
	 * @return string
	 */
	public function getExcelFile(): string {
		return $this->excel_file;
	}


	/**
	 * @return int
	 */
	public function getCountSkipTopRows(): int {
		return $this->count_skip_top_rows;
	}


	/**
	 * @return string
	 */
	public function getMapExistsUsersField(): string {
		return $this->map_exists_users_field;
	}


	/**
	 * @return bool
	 */
	public function isCreateNewUsers(): bool {
		return $this->create_new_users;
	}


	/**
	 * @return string[]
	 */
	public function getMappingFields(): array {
		return $this->mapping_fields;
	}
}
