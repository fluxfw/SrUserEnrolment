<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ExcelImportGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI: ilUIPluginRouterGUI
 */
class ExcelImportGUI {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const CMD_SELECT_FILE = "selectFile";
	const CMD_UPLOAD_FILE = "uploadFile";
	const CMD_BACK_TO_MEMBERS_LIST = "backToMembersList";
	const LANG_MODULE_EXCEL_IMPORT = "excel_import";


	/**
	 * ExcelImportGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->currentUserHasRole()
			|| !self::dic()->access()->checkAccess("write", "", self::rules()->getRefId())) {
			die();
		}

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_SELECT_FILE:
					case self::CMD_UPLOAD_FILE:
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
	 * @return UploadFormGUI
	 */
	protected function getUploadLibraryForm(): UploadFormGUI {
		$form = new UploadFormGUI($this);

		return $form;
	}


	/**
	 *
	 */
	protected function selectFile()/*: void*/ {
		$form = $this->getUploadLibraryForm();

		self::output()->output($form, true);
	}


	/**
	 *
	 */
	protected function uploadFile()/*: void*/ {
		$form = $this->getUploadLibraryForm();

		if (!$form->storeForm()) {
			self::output()->output($form, true);

			return;
		}

		$excel_file = $form->getExcelFile();

		self::output()->output([
			"TODO: ",
			"<br><br>",
			"Missing test excel file!!!",
			"<br><br>",
			$excel_file
		], true);
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
