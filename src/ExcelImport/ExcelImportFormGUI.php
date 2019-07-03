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
use srag\Plugins\SrUserEnrolment\Config\Config;
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
	const KEY_COUNT_SKIP_TOP_ROWS = self::LANG_MODULE . "_count_skip_top_rows";
	const KEY_CREATE_NEW_USERS = self::LANG_MODULE . "_create_new_users";
	const KEY_FIELD_EMAIL = self::LANG_MODULE . "_email";
	const KEY_FIELD_FIRST_NAME = self::LANG_MODULE . "_first_name";
	const KEY_FIELD_GENDER = self::LANG_MODULE . "_gender";
	const KEY_FIELD_GENDER_F = self::LANG_MODULE . "_gender_f";
	const KEY_FIELD_GENDER_M = self::LANG_MODULE . "_gender_m";
	const KEY_FIELD_GENDER_N = self::LANG_MODULE . "_gender_n";
	const KEY_FIELD_LAST_NAME = self::LANG_MODULE . "_last_name";
	const KEY_FIELD_LOGIN = self::LANG_MODULE . "_login";
	const KEY_FIELD_PASSWORD = self::LANG_MODULE . "_password";
	const KEY_MAP_EXISTS_USERS_FIELD = self::LANG_MODULE . "_map_exists_users_field";
	const KEY_MAPPING_FIELDS = self::LANG_MODULE . "_mapping_fields";
	const KEY_SET_PASSWORD = self::LANG_MODULE . "_set_password";
	const SET_PASSWORD_RANDOM = 1;
	const SET_PASSWORD_FIELD = 2;
	/**
	 * @var string
	 */
	protected $excel_file = "";
	/**
	 * @var int
	 */
	protected $excel_import_count_skip_top_rows = 0;
	/**
	 * @var bool
	 */
	protected $excel_import_create_new_users = false;
	/**
	 * @var string
	 */
	protected $excel_import_gender_f = "";
	/**
	 * @var string
	 */
	protected $excel_import_gender_m = "";
	/**
	 * @var string
	 */
	protected $excel_import_gender_n = "";
	/**
	 * @var string
	 */
	protected $excel_import_map_exists_users_field = "";
	/**
	 * @var string[]
	 */
	protected $excel_import_mapping_fields = [];
	/**
	 * @var int
	 */
	protected $excel_import_set_password = 0;


	/**
	 * @return array
	 */
	public static function getExcelImportFields(): array {
		return [
				self::KEY_COUNT_SKIP_TOP_ROWS => [
					self::PROPERTY_CLASS => ilNumberInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					"setTitle" => self::plugin()->translate(self::KEY_COUNT_SKIP_TOP_ROWS),
					"setSuffix" => self::plugin()->translate("rows", static::LANG_MODULE)
				],
				self::KEY_MAP_EXISTS_USERS_FIELD => [
					self::PROPERTY_CLASS => ilRadioGroupInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					self::PROPERTY_SUBITEMS => [
						self::KEY_FIELD_LOGIN => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => self::plugin()->translate(self::KEY_FIELD_LOGIN)
						],
						self::KEY_FIELD_EMAIL => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => self::plugin()->translate(self::KEY_FIELD_EMAIL)
						]
					],
					"setTitle" => self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD)
				],
				self::KEY_SET_PASSWORD => [
					self::PROPERTY_CLASS => ilRadioGroupInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					self::PROPERTY_SUBITEMS => [
						self::SET_PASSWORD_RANDOM => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => self::plugin()->translate(self::KEY_SET_PASSWORD . "_random")
						],
						self::SET_PASSWORD_FIELD => [
							self::PROPERTY_CLASS => ilRadioOption::class,
							"setTitle" => self::plugin()->translate(self::KEY_SET_PASSWORD . "_field")
						]
					],
					"setTitle" => self::plugin()->translate(self::KEY_SET_PASSWORD)
				],
				self::KEY_CREATE_NEW_USERS => [
					self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
					"setTitle" => self::plugin()->translate(self::KEY_CREATE_NEW_USERS)
				],
				"fields_title" => [
					self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class,
					"setTitle" => self::plugin()->translate("fields_title", self::LANG_MODULE)
				]
			] + array_map(function (string $key): array {
				return [
					self::PROPERTY_CLASS => ilTextInputGUI::class,
					"setTitle" => self::plugin()->translate($key)
				];
			}, [
				self::KEY_FIELD_LOGIN => self::KEY_FIELD_LOGIN,
				self::KEY_FIELD_EMAIL => self::KEY_FIELD_EMAIL,
				self::KEY_FIELD_FIRST_NAME => self::KEY_FIELD_FIRST_NAME,
				self::KEY_FIELD_LAST_NAME => self::KEY_FIELD_LAST_NAME,
				self::KEY_FIELD_GENDER => self::KEY_FIELD_GENDER,
				self::KEY_FIELD_GENDER_M => self::KEY_FIELD_GENDER_M,
				self::KEY_FIELD_GENDER_F => self::KEY_FIELD_GENDER_F,
				self::KEY_FIELD_GENDER_N => self::KEY_FIELD_GENDER_N,
				self::KEY_FIELD_PASSWORD => self::KEY_FIELD_PASSWORD
			]);
	}


	/**
	 * @param PropertyFormGUI $form
	 *
	 * @return bool
	 */
	public static function validateExcelImport(PropertyFormGUI $form): bool {
		if (empty($form->getInput($form->getInput(self::KEY_MAP_EXISTS_USERS_FIELD)))) {
			$form->getItemByPostVar($form->getInput(self::KEY_MAP_EXISTS_USERS_FIELD))->setAlert(self::plugin()
				->translate("missing_field_for_map_exists_users", self::LANG_MODULE));

			return false;
		}

		if (intval($form->getInput(self::KEY_SET_PASSWORD)) === self::SET_PASSWORD_FIELD) {
			if (empty($form->getInput(self::KEY_FIELD_PASSWORD))) {
				$form->getItemByPostVar(self::KEY_FIELD_PASSWORD)->setAlert(self::plugin()
					->translate("missing_field_for_set_password", self::LANG_MODULE));

				return false;
			}
		}

		if ($form->getInput(self::KEY_CREATE_NEW_USERS)) {
			$error = false;

			foreach ([ self::KEY_FIELD_LOGIN, self::KEY_FIELD_EMAIL, self::KEY_FIELD_FIRST_NAME, self::KEY_FIELD_LAST_NAME ] as $key) {
				if (empty($form->getInput($key))) {
					$form->getItemByPostVar($key)->setAlert(self::plugin()->translate("missing_field_for_create_new_users", self::LANG_MODULE));

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
	protected function getValue(/*string*/ $key) {
		switch ($key) {
			case "excel_file":
				return $this->{$key};

			default:
				return Config::getField($key);
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function initAction()/*: void*/ {
		self::dic()->ctrl()->saveParameter($this->parent, Repository::GET_PARAM_REF_ID);

		parent::initAction();
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		$this->addCommandButton(ExcelImportGUI::CMD_EXCEL_IMPORT, $this->txt("import", self::LANG_MODULE));
		$this->addCommandButton(ExcelImportGUI::CMD_BACK_TO_MEMBERS_LIST, $this->txt("cancel", self::LANG_MODULE));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
				"excel_file" => [
					self::PROPERTY_CLASS => ilFileInputGUI::class,
					self::PROPERTY_REQUIRED => true,
					"setSuffixes" => [ [ "xlsx", "xltx" ] ]
				]
			] + self::getExcelImportFields();
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
		$this->setTitle($this->txt("title"));
	}


	/**
	 * @inheritDoc
	 */
	public function storeForm(): bool {
		return ($this->storeFormCheck() && self::validateExcelImport($this) && parent::storeForm());
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/ $key, $value)/*: void*/ {
		switch ($key) {
			case "excel_file":
				$this->excel_file = strval($this->getInput("excel_file")["tmp_name"]);
				break;

			case self::KEY_FIELD_EMAIL:
			case self::KEY_FIELD_GENDER:
			case self::KEY_FIELD_FIRST_NAME:
			case self::KEY_FIELD_LAST_NAME:
			case self::KEY_FIELD_LOGIN:
			case self::KEY_FIELD_PASSWORD:
				$this->{self::KEY_MAPPING_FIELDS}[$key] = $value;
				break;

			default:
				$this->{$key} = $value;
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
		return $this->{self::KEY_COUNT_SKIP_TOP_ROWS};
	}


	/**
	 * @return string
	 */
	public function getGenderF(): string {
		return $this->{self::KEY_FIELD_GENDER_F};
	}


	/**
	 * @return string
	 */
	public function getGenderM(): string {
		return $this->{self::KEY_FIELD_GENDER_M};
	}


	/**
	 * @return string
	 */
	public function getGenderN(): string {
		return $this->{self::KEY_FIELD_GENDER_N};
	}


	/**
	 * @return string
	 */
	public function getMapExistsUsersField(): string {
		return $this->{self::KEY_MAP_EXISTS_USERS_FIELD};
	}


	/**
	 * @return string[]
	 */
	public function getMappingFields(): array {
		return $this->{self::KEY_MAPPING_FIELDS};
	}


	/**
	 * @return bool
	 */
	public function isCreateNewUsers(): bool {
		return $this->{self::KEY_CREATE_NEW_USERS};
	}


	/**
	 * @return int
	 */
	public function getSetPassword(): int {
		return $this->excel_import_set_password;
	}
}
