<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Enrolled
 *
 * @package srag\Plugins\SrUserEnrolment\Enroll
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Enrolled extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "srusrenr_enrolled";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName(): string {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName(): string {
		return self::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 * @con_is_notnull  true
	 */
	protected $rule_id;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 * @con_is_notnull  true
	 */
	protected $object_id;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 * @con_is_notnull  true
	 */
	protected $user_id;


	/**
	 * Enrolled constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null) {
		//parent::__construct($primary_key_value, $connector);
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
	 * @return int
	 */
	public function getUserId(): int {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId(int $user_id)/*: void*/ {
		$this->user_id = $user_id;
	}
}
