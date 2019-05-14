<?php

namespace srag\Plugins\SrUserEnrolment\Job;

use ilCronJob;
use ilCronJobResult;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Enroll\Enroller;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Job
 *
 * @package srag\Plugins\SrUserEnrolment\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Job extends ilCronJob {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


	/**
	 * Job constructor
	 */
	public function __construct() {

	}


	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId(): string {
		return self::CRON_JOB_ID;
	}


	/**
	 * @return string
	 */
	public function getTitle(): string {
		return ilSrUserEnrolmentPlugin::PLUGIN_NAME;
	}


	/**
	 * @return string
	 */
	public function getDescription(): string {
		return "";
	}


	/**
	 * Is to be activated on "installation"
	 *
	 * @return boolean
	 */
	public function hasAutoActivation(): bool {
		return true;
	}


	/**
	 * Can the schedule be configured?
	 *
	 * @return boolean
	 */
	public function hasFlexibleSchedule(): bool {
		return true;
	}


	/**
	 * Get schedule type
	 *
	 * @return int
	 */
	public function getDefaultScheduleType(): int {
		return self::SCHEDULE_TYPE_IN_HOURS;
	}


	/**
	 * Get schedule value
	 *
	 * @return int|array
	 */
	public function getDefaultScheduleValue(): int {
		return 12;
	}


	/**
	 * Run job
	 *
	 * @return ilCronJobResult
	 */
	public function run(): ilCronJobResult {
		$result = new ilCronJobResult();

		$enroller = new Enroller(self::rules()->getRules(), self::ilias()->users()->getUsers(), self::ilias()->orgUnits()->getOrgUnits());

		$result_count = $enroller->run();

		$result->setStatus(ilCronJobResult::STATUS_OK);

		$result->setMessage($result_count);

		return $result;
	}
}
