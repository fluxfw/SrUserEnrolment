<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilObject;
use ilObjOrgUnit;
use ilObjOrgUnitTree;
use ilOrgUnitPosition;
use ilOrgUnitUserAssignment;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

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
	 * @param int  $obj_id
	 * @param bool $also_children
	 *
	 * @return int[]
	 */
	public function getRefIdsSelfAndChildren(int $obj_id, bool $also_children = true): array {
		$children = [];

		foreach (ilObject::_getAllReferences($obj_id) as $ref_id) {
			$children[] = $ref_id;

			if ($also_children) {
				$children = array_merge($children, ilObjOrgUnitTree::_getInstance()->getAllChildren($ref_id));
			}
		}

		return array_unique($children);
	}


	/**
	 * @return ilObjOrgUnit[]
	 */
	public function getOrgUnits(): array {
		$result = self::dic()->database()->queryF('SELECT obj_id FROM object_data WHERE type=%s', [ "text" ], [ "orgu" ]);

		$array = [];

		while (($row = $result->fetchAssoc()) !== false) {
			$array[] = new ilObjOrgUnit($row["obj_id"], false);
		}

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


	/**
	 * @param int $user_id
	 * @param int $org_unit_id
	 * @param int $position_id
	 *
	 * @return bool
	 */
	public function hasUserPosition(int $user_id, int $org_unit_id, int $position_id): bool {
		return (ilOrgUnitUserAssignment::where([
				"user_id" => $user_id,
				"position_id" => $position_id,
				"orgu_id" => $org_unit_id,
			])->first() !== null);
	}
}
