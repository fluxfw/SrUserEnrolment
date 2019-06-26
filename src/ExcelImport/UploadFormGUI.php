<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilFileInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class UploadFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UploadFormGUI extends PropertyFormGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const LANG_MODULE = ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT;


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/ $key)/*: void*/ {

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
		$this->addCommandButton(ExcelImportGUI::CMD_UPLOAD_FILE, self::plugin()->translate("upload", self::LANG_MODULE));
		$this->addCommandButton(ExcelImportGUI::CMD_BACK_TO_MEMBERS_LIST, self::plugin()->translate("cancel", self::LANG_MODULE));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			"excel_file" => [
				self::PROPERTY_CLASS => ilFileInputGUI::class,
				self::PROPERTY_REQUIRED => true,
				"setSuffixes" => [ [ "xlsx" ] ]
			]
		];
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
		$this->setTitle(self::plugin()->translate("title", self::LANG_MODULE));
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/ $key, $value)/*: void*/ {

	}


	/**
	 * @return string
	 */
	public function getExcelFile(): string {
		return strval($this->getInput("excel_file")["tmp_name"]);
	}
}
