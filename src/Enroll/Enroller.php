<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilException;
use ilObjCourse;
use ilObjOrgUnit;
use ilObjRole;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
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
	 * @var ilObjOrgUnit[]
	 */
	protected $org_units;


	/**
	 * Enroller constructor
	 *
	 * @param Rule[]         $rules
	 * @param ilObjUser[]    $users
	 * @param ilObjOrgUnit[] $org_units
	 */
	public function __construct(array $rules, array $users, array $org_units) {
		$this->rules = $rules;
		$this->users = $users;
		$this->org_units = $org_units;
	}


	/**
	 * @return string
	 */
	public function run(): string {
		$rules_count = 0;

		foreach ($this->rules as $rule) {

			try {

				$course = new ilObjCourse($rule->getObjectId(), false);

				foreach ($this->users as $user) {

					try {

						if (!self::enrolleds()->hasEnrolled($rule->getRuleId(), $rule->getObjectId(), $user->getId())) {

							foreach ($this->org_units as $org_unit) {

								try {

									if ($this->check($rule, $org_unit, $user)) {

										$this->enroll($course, $user);

										self::enrolleds()->enrolled($rule->getRuleId(), $rule->getObjectId(), $user->getId());

										self::logs()->storeLog(self::logs()->factory()->log()->withObjectId($rule->getObjectId())
											->withRuleId($rule->getRuleId())->withUserId($user->getId())->withMessage(""));
									}
								} catch (Throwable $ex) {
									self::logs()->storeLog(self::logs()->factory()->log()->withObjectId($rule->getObjectId())
										->withRuleId($rule->getRuleId())->withUserId($user->getId())->withStatus(Log::STATUS_ERROR)
										->withMessage($ex->getMessage()));
								}
							}
						}
					} catch (Throwable $ex) {
						self::logs()->storeLog(self::logs()->factory()->log()->withObjectId($rule->getObjectId())->withRuleId($rule->getRuleId())
							->withUserId($user->getId())->withStatus(Log::STATUS_ERROR)->withMessage($ex->getMessage()));
					}
				}
			} catch (Throwable $ex) {
				self::logs()->storeLog(self::logs()->factory()->log()->withObjectId($rule->getObjectId())->withRuleId($rule->getRuleId())
					->withStatus(Log::STATUS_ERROR)->withMessage($ex->getMessage()));
			}

			$rules_count ++;
		}

		$logs = array_reduce(Log::$statuss, function (array $logs, int $status): array {
			$logs[] = self::plugin()->translate("status_" . $status, LogsGUI::LANG_MODULE_LOGS) . ": " . count(self::logs()->getKeptLogs($status));

			return $logs;
		}, [
			self::plugin()->translate("rules", LogsGUI::LANG_MODULE_LOGS) . ": " . $rules_count
		]);

		return implode("<br>", $logs);
	}


	/**
	 * @param Rule         $rule
	 * @param ilObjUser    $user
	 * @param ilObjOrgUnit $org_unit
	 *
	 * @return bool
	 */
	protected function check(Rule $rule, ilObjOrgUnit $org_unit, ilObjUser $user): bool {
		switch ($rule->getOrgUnitType()) {
			case Rule::ORG_UNIT_TYPE_TITLE:
				$org_unit_title = $org_unit->getTitle();
				$match_title = $rule->getTitle();

				if (!$rule->isOperatorCaseSensitive()) {
					$org_unit_title = strtolower($org_unit_title);
					$match_title = strtolower($match_title);
				}

				switch ($rule->getOperator()) {
					case Rule::OPERATOR_EQUALS:
						$check = ($org_unit_title === $match_title);
						break;

					case Rule::OPERATOR_STARTS_WITH:
						$check = (strpos($org_unit_title, $match_title) === 0);
						break;

					case Rule::OPERATOR_CONTAINS:
						$check = (strpos($org_unit_title, $match_title) !== false);
						break;

					case Rule::OPERATOR_ENDS_WITH:
						$check = (strrpos($org_unit_title, $match_title) === (strlen($org_unit_title) - strlen($match_title)));
						break;

					case Rule::OPERATOR_IS_EMPTY:
						$check = empty($org_unit_title);
						break;

					case Rule::OPERATOR_REG_EX:
						// Fix RegExp
						if ($match_title[0] !== "/" && $match_title[strlen($match_title) - 1] !== "/") {
							$match_title = "/$match_title/";
						}
						$check = (preg_match($match_title, $org_unit_title) === 1);
						break;

					case Rule::OPERATOR_LESS:
						$check = ($org_unit_title < $match_title);
						break;

					case Rule::OPERATOR_LESS_EQUALS:
						$check = ($org_unit_title <= $match_title);
						break;

					case Rule::OPERATOR_BIGGER:
						$check = ($org_unit_title > $match_title);
						break;

					case Rule::OPERATOR_BIGGER_EQUALS:
						$check = ($org_unit_title >= $match_title);
						break;

					default:
						return false;
				}

				if ($rule->isOperatorNegated()) {
					$check = (!$check);
				}

				if (!$check) {
					return false;
				}
				break;

			case Rule::ORG_UNIT_TYPE_TREE:
				if (!in_array($rule->getRefId(), self::ilias()->orgUnits()->getRefIdsSelfAndChildren($org_unit->getId(), ($rule->getOperator()
					=== Rule::OPERATOR_EQUALS_SUBSEQUENT)))) {
					return false;
				}
				break;

			default:
				return false;
		}

		return ($rule->getPosition() === Rule::POSITION_ALL
			|| self::ilias()->orgUnits()->hasUserPosition($user->getId(), $org_unit->getId(), $rule->getPosition()));
	}


	/**
	 * @param ilObjCourse $course
	 * @param ilObjUser   $user
	 *
	 * @throws SrUserEnrolmentException
	 */
	protected function enroll(ilObjCourse $course, ilObjUser $user)/*: void*/ {
		if ($course->getMembersObject()->isAssigned($user->getId())) {
			throw new SrUserEnrolmentException("User " . $user->getFullname() . " already assigned as role "
				. implode(", ", array_map(function (int $role_id): string {
					return ilObjRole::_getTranslation(self::dic()->objDataCache()->lookupTitle($role_id));
				}, $course->getMembersObject()->getAssignedRoles($user->getId()))) . " in course " . $course->getTitle());
		}

		$course->getMembersObject()->add($user->getId(), IL_CRS_MEMBER);
	}
}
