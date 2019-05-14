<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Rule
 *
 * @package srag\Plugins\SrUserEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Rule extends ActiveRecord {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const TABLE_NAME = "srusrenr_rule";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const ORG_UNIT_TYPE_TITLE = 1;
	const ORG_UNIT_TYPE_TREE = 2;
	const OPERATOR_EQUALS = 1;
	const OPERATOR_STARTS_WITH = 2;
	const OPERATOR_CONTAINS = 3;
	const OPERATOR_ENDS_WITH = 4;
	const OPERATOR_IS_EMPTY = 5;
	const OPERATOR_REG_EX = 6;
	const OPERATOR_LESS = 7;
	const OPERATOR_LESS_EQUALS = 8;
	const OPERATOR_BIGGER = 9;
	const OPERATOR_BIGGER_EQUALS = 10;
	const OPERATOR_EQUALS_SUBSEQUENT = 11;
	const POSITION_ALL = 0;
	/**
	 * @var array
	 */
	public static $operators_title = [
		self::OPERATOR_EQUALS => "equals",
		self::OPERATOR_STARTS_WITH => "starts_with",
		self::OPERATOR_CONTAINS => "contains",
		self::OPERATOR_ENDS_WITH => "ends_with",
		self::OPERATOR_IS_EMPTY => "is_empty",
		self::OPERATOR_REG_EX => "reg_ex",
		self::OPERATOR_LESS => "less",
		self::OPERATOR_LESS_EQUALS => "less_equals",
		self::OPERATOR_BIGGER => "bigger",
		self::OPERATOR_BIGGER_EQUALS => "bigger_equals"
	];
	/**
	 * @var array
	 */
	public static $operators_ref_id = [
		self::OPERATOR_EQUALS => "equals",
		self::OPERATOR_EQUALS_SUBSEQUENT => "equals_subsequent"
	];


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 * @con_sequence     true
	 */
	protected $rule_id;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $object_id;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $enabled = false;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $org_unit_type = self::ORG_UNIT_TYPE_TITLE;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $title = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       2
	 * @con_is_notnull   true
	 */
	protected $operator = 0;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $operator_negated = false;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $operator_case_sensitive = false;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $ref_id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $position = self::POSITION_ALL;


	/**
	 * Rule constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep(/*string*/ $field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "enabled":
			case "operator_negated":
			case "operator_case_sensitive":
				return ($field_value ? 1 : 0);

			default:
				return null;
		}
	}


	/**
	 * @param string $field_name
	 * @param mixed  $field_value
	 *
	 * @return mixed|null
	 */
	public function wakeUp(/*string*/ $field_name, $field_value) {
		switch ($field_name) {
			case "object_id":
			case "operator":
			case "org_unit_type":
			case "position":
			case "ref_id":
			case "rule_id":
				return intval($field_value);

			case "enabled":
			case "operator_negated":
			case "operator_case_sensitive":
				return boolval($field_value);

			default:
				return null;
		}
	}


	/**
	 * @return int
	 */
	public function getRuleId(): int {
		return $this->rule_id;
	}


	/**
	 * @param int $rule_id
	 */
	public function setRuleId(int $rule_id)/*: void*/ {
		$this->rule_id = $rule_id;
	}


	/**
	 * @return int
	 */
	public function getObjectId(): int {
		return $this->object_id;
	}


	/**
	 * @param int $object_id
	 */
	public function setObjectId(int $object_id)/*: void*/ {
		$this->object_id = $object_id;
	}


	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->enabled;
	}


	/**
	 * @param bool $enabled
	 */
	public function setEnabled(bool $enabled)/*: void*/ {
		$this->enabled = $enabled;
	}


	/**
	 * @return int
	 */
	public function getOrgUnitType(): int {
		return $this->org_unit_type;
	}


	/**
	 * @param int $org_unit_type
	 */
	public function setOrgUnitType(int $org_unit_type)/*: void*/ {
		$this->org_unit_type = $org_unit_type;
	}


	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle(string $title)/*: void*/ {
		$this->title = $title;
	}


	/**
	 * @return int
	 */
	public function getOperator(): int {
		return $this->operator;
	}


	/**
	 * @param int $operator
	 */
	public function setOperator(int $operator)/*: void*/ {
		$this->operator = $operator;
	}


	/**
	 * @return bool
	 */
	public function isOperatorNegated(): bool {
		return $this->operator_negated;
	}


	/**
	 * @param bool $operator_negated
	 */
	public function setOperatorNegated(bool $operator_negated)/*: void*/ {
		$this->operator_negated = $operator_negated;
	}


	/**
	 * @return bool
	 */
	public function isOperatorCaseSensitive(): bool {
		return $this->operator_case_sensitive;
	}


	/**
	 * @param bool $operator_case_sensitive
	 */
	public function setOperatorCaseSensitive(bool $operator_case_sensitive)/*: void*/ {
		$this->operator_case_sensitive = $operator_case_sensitive;
	}


	/**
	 * @return int
	 */
	public function getRefId(): int {
		return $this->ref_id;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId(int $ref_id)/*: void*/ {
		$this->ref_id = $ref_id;
	}


	/**
	 * @return int
	 */
	public function getPosition(): int {
		return $this->position;
	}


	/**
	 * @param int $position
	 */
	public function setPosition(int $position)/*: void*/ {
		$this->position = $position;
	}
}
