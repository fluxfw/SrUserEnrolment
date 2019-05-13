<?php

namespace srag\Plugins\SrUserEnrolment\Utils;

use srag\Plugins\SrUserEnrolment\Access\Access;
use srag\Plugins\SrUserEnrolment\Access\Ilias;

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
	 * @return Ilias
	 */
	protected static function ilias(): Ilias {
		return Ilias::getInstance();
	}
}
