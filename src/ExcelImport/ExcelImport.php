<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilExcel;
use ilObjCourse;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

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
	/**
	 * @var ExcelImportFormGUI
	 */
	protected $form;


	/**
	 * ExcelImport constructor
	 *
	 * @param ExcelImportFormGUI $form
	 */
	public function __construct(ExcelImportFormGUI $form) {
		$this->form = $form;
	}


	/**
	 *
	 */
	public function excelImport()/*: void*/ {
		$excel = new ilExcel();

		$excel->loadFromFile($this->form->getExcelFile());

		$rows = $excel->getSheetAsArray();

		$rows = array_slice($rows, $this->form->getCountSkipTopRows());

		$columns_map = array_flip(array_filter($this->form->getMappingFields()));

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

		$exists_users = array_filter($users, function (stdClass &$user): bool {
			switch ($this->form->getMapExistsUsersField()) {
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

		if ($this->form->isCreateNewUsers()) {
			$new_users = array_filter($users, function (stdClass $user): bool {
				return empty($user->ilias_user_id);
			});
		} else {
			$new_users = [];
		}

		die();

		// TODO: Confirmation form

		if ($this->form->isCreateNewUsers()) {
			foreach ($new_users as &$user) {
				$user->ilias_user_id = self::ilias()->users()
					->createNewAccount(strval($user->login), strval($user->email), strval($user->first_name), strval($user->last_name), strval($user->gender));

				$exists_users[] = $user;
			}
		}

		$course = new ilObjCourse(self::rules()->getRefId());

		foreach ($exists_users as $user) {
			self::ilias()->courses()->enrollMemberToCourse($course, $user->ilias_user_id, $user->first_name . " " . $user->last_name);
		}
	}
}
