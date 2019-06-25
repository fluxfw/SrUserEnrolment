<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilObjCourse;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use Throwable;

/**
 * Class Enroller
 *
 * @package srag\Plugins\SrUserEnrolment\Enroll
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Enroller {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	/**
	 * @var Rule[]
	 */
	protected $rules;
	/**
	 * @var ilObjUser[]
	 */
	protected $users;


	/**
	 * Enroller constructor
	 *
	 * @param Rule[]      $rules
	 * @param ilObjUser[] $users
	 */
	public function __construct(array $rules, array $users) {
		$this->rules = $rules;
		$this->users = $users;
	}


	/**
	 * @return string
	 */
	public function run(): string {
		/**
		 * @var Enroll[] $enrolls
		 */
		$enrolls = [];

		foreach ($this->rules as $rule) {

			try {
				$object = new ilObjCourse($rule->getObjectId(), false);

				foreach (self::ilias()->orgUnits()->getOrgUnitUsers($rule) as $user) {
					$enrolls[$object->getId() . "_" . $user->getId()] = new Enroll($rule, $user, $object);
				}
			} catch (Throwable $ex) {
				self::logs()->storeLog(self::logs()->factory()->exceptionLog($ex, $rule->getObjectId(), $rule->getRuleId()));
			}
		}

		foreach ($enrolls as $enroll) {
			$enroll->enroll();
		}

		$logs = array_reduce(Log::$statuss, function (array $logs, int $status): array {
			$logs[] = self::plugin()->translate("status_" . $status, LogsGUI::LANG_MODULE_LOGS) . ": " . count(self::logs()->getKeptLogs($status));

			return $logs;
		}, [
			self::plugin()->translate("rules", LogsGUI::LANG_MODULE_LOGS) . ": " . count($this->rules)
		]);

		return implode("<br>", $logs);
	}
}
