<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilObjCourse;
use ilObjRole;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
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

				$this->enroll_();

				self::enrolleds()->enrolled($this->rule->getRuleId(), $this->object->getId(), $this->user->getId());

				self::logs()->storeLog(self::logs()->factory()
					->objectRuleUserLog($this->object->getId(), $this->rule->getRuleId(), $this->user->getId())->withStatus(Log::STATUS_ADD));
			}
		} catch (Throwable $ex) {
			self::logs()->storeLog(self::logs()->factory()
				->exceptionLog($ex, $this->object->getId(), $this->rule->getRuleId(), $this->user->getId()));
		}
	}


	/**
	 * @throws SrUserEnrolmentException
	 */
	protected function enroll_()/*: void*/ {
		if ($this->object->getMembersObject()->isAssigned($this->user->getId())) {
			throw new SrUserEnrolmentException("User " . $this->user->getFullname() . " already assigned as role "
				. implode(", ", array_map(function (int $role_id): string {
					return ilObjRole::_getTranslation(self::dic()->objDataCache()->lookupTitle($role_id));
				}, $this->object->getMembersObject()->getAssignedRoles($this->user->getId()))) . " in course " . $this->object->getTitle());
		}

		$this->object->getMembersObject()->add($this->user->getId(), IL_CRS_MEMBER);
	}
}
