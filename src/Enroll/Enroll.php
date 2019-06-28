<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilObjCourse;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use Throwable;

/**
 * Class Enroll
 *
 * @package srag\Plugins\SrUserEnrolment\Enroll
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Enroll {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	/**
	 * @var Rule
	 */
	protected $rule;
	/**
	 * @var ilObjUser
	 */
	protected $user;
	/**
	 * @var ilObjCourse
	 */
	protected $object;


	/**
	 * Enroll constructor
	 *
	 * @param Rule        $rule
	 * @param ilObjUser   $user
	 * @param ilObjCourse $object
	 */
	public function __construct(Rule $rule, ilObjUser $user, ilObjCourse $object) {
		$this->rule = $rule;
		$this->user = $user;
		$this->object = $object;
	}


	/**
	 *
	 */
	public function enroll()/*: void*/ {
		try {
			if (!self::enrolleds()->hasEnrolled($this->rule->getRuleId(), $this->object->getId(), $this->user->getId())) {

				self::ilias()->courses()->enrollMemberToCourse($this->object, $this->user->getId(), $this->user->getFullname());

				self::enrolleds()->enrolled($this->rule->getRuleId(), $this->object->getId(), $this->user->getId());

				self::logs()->storeLog(self::logs()->factory()
					->objectRuleUserLog($this->object->getId(), $this->rule->getRuleId(), $this->user->getId())->withStatus(Log::STATUS_ENROLLED));
			}
		} catch (Throwable $ex) {
			self::logs()->storeLog(self::logs()->factory()
				->exceptionLog($ex, $this->object->getId(), $this->rule->getRuleId(), $this->user->getId()));
		}
	}
}
