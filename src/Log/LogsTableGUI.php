<?php

namespace srag\Plugins\SrUserEnrolment\Log;

use ilDateTime;
use ilObjUser;
use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\DateDurationInputGUI\DateDurationInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class LogsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Log
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LogsTableGUI extends TableGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const LANG_MODULE = LogsGUI::LANG_MODULE_LOGS;


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT): string {
		switch ($column) {
			case "status":
				$column = $this->txt("status_" . $row[$column]);
				break;

			case "user_id":
				$column = ilObjUser::_lookupLogin($row[$column]);
				break;

			default:
				$column = $row[$column];
				break;
		}

		return strval($column);
	}


	/**
	 * @inheritdoc
	 */
	public function getSelectableColumns2(): array {
		$columns = [
			"date" => "date",
			"status" => "status",
			"message" => "message",
			"user_id" => "user_id"
		];

		$columns = array_map(function (string $key): array {
			return [
				"id" => $key,
				"default" => true,
				"sort" => true
			];
		}, $columns);

		return $columns;
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setExternalSegmentation(true);
		$this->setExternalSorting(true);

		$this->setDefaultOrderField("date");
		$this->setDefaultOrderDirection("desc");

		// Fix stupid ilTable2GUI !!! ...
		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$filter = $this->getFilterValues();

		$message = $filter["message"];
		$date_start = $filter["date"]["start"];
		if (!empty($date_start)) {
			$date_start = new ilDateTime(intval($date_start), IL_CAL_UNIX);
		} else {
			$date_start = null;
		}
		$date_end = $filter["date"]["end"];
		if (!empty($date_end)) {
			$date_end = new ilDateTime(intval($date_end), IL_CAL_UNIX);
		} else {
			$date_end = null;
		}
		$status = $filter["status"];
		if (!empty($status)) {
			$status = intval($status);
		} else {
			$status = null;
		}

		$columns = array_keys($this->getSelectedColumns());

		$this->setData(self::logs()->getLogs(self::rules()
			->getObjId(), $columns, $this->getOrderField(), $this->getOrderDirection(), intval($this->getOffset()), intval($this->getLimit()), $message, $date_start, $date_end, $status));

		$this->setMaxCount(self::logs()->getLogsCount(self::rules()->getObjId(), $message, $date_start, $date_end, $status));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
		self::dic()->language()->loadLanguageModule("form");

		$this->filter_fields = [
			"date" => [
				PropertyFormGUI::PROPERTY_CLASS => DateDurationInputGUI::class,
				"setShowTime" => true
			],
			"message" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
			],
			"status" => [
				PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
				PropertyFormGUI::PROPERTY_OPTIONS => [
						"" => "",
					] + array_map(function (int $status): string {
						return $this->txt("status_" . $status);
					}, Log::$statuss)
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srusrenr_logs");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle($this->txt("logs"));
	}
}
