<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Rules
 *
 * @package srag\Plugins\SrUserEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Rules {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const GET_PARAM_REF_ID = "ref_id";
	const GET_PARAM_TARGET = "target";
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
	 * Rules constructor
	 */
	private function __construct() {

	}


	/**
	 * @return int|null
	 */
	public function getRefId()/*: ?int*/ {
		$obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

		if ($obj_ref_id === null) {
			$param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

			$obj_ref_id = explode("_", $param_target)[1];
		}

		$obj_ref_id = intval($obj_ref_id);

		if ($obj_ref_id > 0) {
			return $obj_ref_id;
		} else {
			return null;
		}
	}


	/**
	 * @param int $rule_id
	 *
	 * @return Rule|null
	 */
	public function getRuleById(int $rule_id)/*: ?Rule*/ {
		/**
		 * @var Rule|null $rule
		 */

		$rule = Rule::where([ "rule_id" => $rule_id ])->first();

		return $rule;
	}


	/**
	 * @param int $ref_id
	 *
	 * @return array
	 */
	public function getRulesArray(int $ref_id): array {
		return Rule::where([ "ref_id" => $ref_id ])->getArray();
	}
}

Rule::updateDB();
