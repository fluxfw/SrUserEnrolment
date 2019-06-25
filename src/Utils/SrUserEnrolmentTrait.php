<?php

namespace srag\Plugins\SrUserEnrolment\Utils;

use srag\Plugins\SrUserEnrolment\Access\Access;
use srag\Plugins\SrUserEnrolment\Access\Ilias;
use srag\Plugins\SrUserEnrolment\Enroll\Repository as EnrollRepository;
use srag\Plugins\SrUserEnrolment\Logs\Repository as LogRepository;
use srag\Plugins\SrUserEnrolment\Rule\Repository as RuleRepository;

/**
 * Trait SrUserEnrolmentTrait
 *
 * @package srag\Plugins\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait SrUserEnrolmentTrait {

	/**
	 * @return Access
	 */
	protected static function access(): Access {
		return Access::getInstance();
	}


	/**
	 * @return EnrollRepository
	 */
	protected static function enrolleds(): EnrollRepository {
		return EnrollRepository::getInstance();
	}


	/**
	 * @return Ilias
	 */
	protected static function ilias(): Ilias {
		return Ilias::getInstance();
	}


	/**
	 * @return LogRepository
	 */
	protected static function logs(): LogRepository {
		return LogRepository::getInstance();
	}


	/**
	 * @return RuleRepository
	 */
	protected static function rules(): RuleRepository {
		return RuleRepository::getInstance();
	}
}
