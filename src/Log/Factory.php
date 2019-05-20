<?php

namespace srag\Plugins\SrUserEnrolment\Logs;

use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\Logs
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Factory constructor
	 */
	private function __construct() {

	}


	/**
	 * @param stdClass $data
	 *
	 * @return Log
	 */
	public function fromDB(stdClass $data): Log {
		return $this->log()->withLogId($data->log_id)->withObjectId($data->object_id)->withRuleId($data->rule_id)->withUserId($data->user_id)
			->withDate(new ilDateTime($data->date, IL_CAL_DATETIME))->withStatus($data->status)->withMessage($data->message);
	}


	/**
	 * @return Log
	 */
	public function log(): Log {
		$log = new Log();

		return $log;
	}
}
