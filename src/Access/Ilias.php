<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilDBStatement;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Ilias
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Ilias {

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
	 * Ilias constructor
	 */
	private function __construct() {

	}


	/**
	 * @return Courses
	 */
	public function courses(): Courses {
		return Courses::getInstance();
	}


	/**
	 * @param string[] $wheres
	 * @param string[] $types
	 * @param string[] $values
	 * @param string[] $selects
	 * @param string   $additional_joins
	 *
	 * @return ilDBStatement
	 */
	public function getObjectFilterStatement(array $wheres, array $types, array $values, array $selects, string $additional_joins = ""): ilDBStatement {
		return self::dic()->database()->queryF('SELECT ' . implode(', ', $selects)
			. ' FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id' . $additional_joins . ' WHERE '
			. implode(' AND ', $wheres), $types, $values);
	}


	/**
	 * @param string[] $wheres
	 * @param string[] $types
	 * @param string[] $values
	 * @param string   $additional_joins
	 *
	 * @return int|null
	 */
	public function getObjectRefIdByFilter(array $wheres, array $types, array $values, string $additional_joins = "")/*: ?int*/ {
		$result = $this->getObjectFilterStatement($wheres, $types, $values, [ "ref_id" ], $additional_joins);

		if ($result->rowCount() === 1) {
			return intval($result->fetchAssoc()["ref_id"]);
		} else {
			return null;
		}
	}


	/**
	 * @return OrgUnits
	 */
	public function orgUnits(): OrgUnits {
		return OrgUnits::getInstance();
	}


	/**
	 * @return Roles
	 */
	public function roles(): Roles {
		return Roles::getInstance();
	}


	/**
	 * @return Users
	 */
	public function users(): Users {
		return Users::getInstance();
	}
}
