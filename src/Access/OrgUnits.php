<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilDBConstants;
use ilObjUser;
use ilOrgUnitPosition;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class OrgUnits
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class OrgUnits {

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
	 * OrgUnits constructor
	 */
	private function __construct() {

	}


	/**
	 * @param Rule $rule
	 *
	 * @return ilObjUser[]
	 */
	public function getOrgUnitUsers(Rule $rule): array {
		$where = [ "type=%s" ];
		$types = [ ilDBConstants::T_TEXT ];
		$values = [ "orgu" ];

		switch ($rule->getOrgUnitType()) {
			case Rule::ORG_UNIT_TYPE_TITLE:
				switch ($rule->getOperator()) {
					case Rule::OPERATOR_EQUALS:
						if ($rule->isOperatorCaseSensitive()) {
							if ($rule->isOperatorNegated()) {
								$where[] = "UPPER(title)!=UPPER(%s)";
							} else {
								$where[] = "UPPER(title)=UPPER(%s)";
							}
						} else {
							if ($rule->isOperatorNegated()) {
								$where[] = "title!=%s";
							} else {
								$where[] = "title=%s";
							}
						}
						$types[] = ilDBConstants::T_TEXT;
						$values[] = $rule->getTitle();
						break;

					case Rule::OPERATOR_STARTS_WITH:
						if ($rule->isOperatorCaseSensitive()) {
							if ($rule->isOperatorNegated()) {
								$where[] = "UPPER(title) NOT LIKE UPPER(%s)";
							} else {
								$where[] = "UPPER(title) LIKE UPPER(%s)";
							}
						} else {
							if ($rule->isOperatorNegated()) {
								$where[] = "title NOT LIKE %s";
							} else {
								$where[] = "title LIKE %s";
							}
						}
						$types[] = ilDBConstants::T_TEXT;
						$values[] = $rule->getTitle() . "%";
						break;

					case Rule::OPERATOR_CONTAINS:
						if ($rule->isOperatorCaseSensitive()) {
							if ($rule->isOperatorNegated()) {
								$where[] = "UPPER(title) NOT LIKE UPPER(%s)";
							} else {
								$where[] = "UPPER(title) LIKE UPPER(%s)";
							}
						} else {
							if ($rule->isOperatorNegated()) {
								$where[] = "title NOT LIKE %s";
							} else {
								$where[] = "title LIKE %s";
							}
						}
						$types[] = ilDBConstants::T_TEXT;
						$values[] = "%" . $rule->getTitle() . "%";
						break;

					case Rule::OPERATOR_ENDS_WITH:
						if ($rule->isOperatorCaseSensitive()) {
							if ($rule->isOperatorNegated()) {
								$where[] = "UPPER(title) NOT LIKE UPPER(%s)";
							} else {
								$where[] = "UPPER(title) LIKE UPPER(%s)";
							}
						} else {
							if ($rule->isOperatorNegated()) {
								$where[] = "title NOT LIKE %s";
							} else {
								$where[] = "title LIKE %s";
							}
						}
						$types[] = ilDBConstants::T_TEXT;
						$values[] = "%" . $rule->getTitle();
						break;

					case Rule::OPERATOR_REG_EX:
						if ($rule->isOperatorCaseSensitive()) {
							if ($rule->isOperatorNegated()) {
								$where[] = "UPPER(title) NOT REGEXP %s";
							} else {
								$where[] = "UPPER(title) REGEXP %s";
							}
						} else {
							if ($rule->isOperatorNegated()) {
								$where[] = "title NOT REGEXP %s";
							} else {
								$where[] = "title REGEXP %s";
							}
						}
						$types = [ ilDBConstants::T_TEXT ];
						$values = [ $rule->getTitle() ];
						break;

					default:
						return [];
				}
				break;

			case Rule::ORG_UNIT_TYPE_TREE:
				$where[] = "ref_id=%s";
				$types[] = ilDBConstants::T_INTEGER;
				$values[] = $rule->getRefId();

				// TODO: ($this->rule->getOperator() === Rule::OPERATOR_EQUALS_SUBSEQUENT)
				break;

			default:
				return [];
		}

		if ($rule->getPosition() !== Rule::POSITION_ALL) {
			$where[] = "position_id=%s";
			$types[] = ilDBConstants::T_INTEGER;
			$values[] = $rule->getPosition();
		}

		$array = self::dic()->database()->fetchAllCallback(self::dic()->database()
			->queryF('SELECT user_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id INNER JOIN il_orgu_ua ON object_data.obj_id=il_orgu_ua.orgu_id WHERE '
				. implode(' AND ', $where), $types, $values), function (stdClass $data): ilObjUser {
			return new ilObjUser($data->user_id);
		});

		return $array;
	}


	/**
	 * @return array
	 */
	public function getPositions(): array {
		return array_map(function (ilOrgUnitPosition $position): string {
			return $position->getTitle();
		}, ilOrgUnitPosition::get());
	}
}
