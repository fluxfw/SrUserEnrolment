<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\Enroll
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
	 * @return Enrolled
	 */
	public function fromDB(stdClass $data): Enrolled {
		$enrolled = $this->newInstance();

		$enrolled->setRuleId($data->rule_id);
		$enrolled->setObjectId($data->object_id);
		$enrolled->setUserId($data->user_id);

		return $enrolled;
	}


	/**
	 * @return Enrolled
	 */
	public function newInstance(): Enrolled {
		$rule = new Enrolled();

		return $rule;
	}
}
