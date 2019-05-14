<?php

namespace srag\Plugins\SrUserEnrolment\Log;

use ActiveRecord;
use arConnector;
use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Log
 *
 * @package srag\Plugins\SrUserEnrolment\Log
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Log extends ActiveRecord {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const TABLE_NAME = "srusrenr_log";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const STATUS_ADD = 100;
	const STATUS_ERROR = 200;


	/**
	 * @return string
	 */
	public final function getConnectorContainerName(): string {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public final static function returnDbTableName(): string {
		return static::TABLE_NAME;
	}


	/**
	 * @var array
	 */
	public static $statuss = [
		self::STATUS_ADD => self::STATUS_ADD,
		self::STATUS_ERROR => self::STATUS_ERROR
	];
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
	protected $log_id;
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
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
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
	protected $user_id;
	/**
	 * @var ilDateTime
	 *
	 * @con_has_field    true
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $date = null;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $status = 0;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $message = "";


	/**
	 * Log constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public final function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null) {
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
			case "date":
				return $field_value->get(IL_CAL_DATETIME);

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
			case "log_id":
			case "object_id":
			case "rule_id":
			case "status":
			case "user_id":
				return intval($field_value);

			case "date":
				return new ilDateTime($field_value, IL_CAL_DATETIME);

			default:
				return null;
		}
	}


	/**
	 *
	 */
	public function create()/*: void*/ {
		if ($this->date === null) {
			$this->date = new ilDateTime(time(), IL_CAL_UNIX);
		}

		parent::create();
	}


	/**
	 * @inheritdoc
	 */
	public function getLogId(): int {
		return $this->log_id;
	}


	/**
	 * @param int $log_id
	 *
	 * @return self
	 */
	public function withLogId(int $log_id): self {
		$this->log_id = $log_id;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getObjectId(): int {
		return $this->object_id;
	}


	/**
	 * @param int $object_id
	 *
	 * @return self
	 */
	public function withObjectId(int $object_id): self {
		$this->object_id = $object_id;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getRuleId(): int {
		return $this->rule_id;
	}


	/**
	 * @param int $rule_id
	 *
	 * @return self
	 */
	public function withRuleId(int $rule_id): self {
		$this->rule_id = $rule_id;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getUserId(): int {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 *
	 * @return self
	 */
	public function withUserId(int $user_id): self {
		$this->user_id = $user_id;

		return $this;
	}


	/**
	 * @return ilDateTime
	 */
	public function getDate(): ilDateTime {
		return $this->date;
	}


	/**
	 * @param ilDateTime $date
	 *
	 * @return self
	 */
	public function withDate(ilDateTime $date): self {
		$this->date = $date;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getStatus(): int {
		return $this->status;
	}


	/**
	 * @param int $status
	 *
	 * @return self
	 */
	public function withStatus(int $status): self {
		$this->status = $status;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getMessage(): string {
		return $this->message;
	}


	/**
	 * @param string $message
	 *
	 * @return self
	 */
	public function withMessage(string $message): self {
		$this->message = $message;

		return $this;
	}


	/**
	 *
	 */
	public function store()/*: void*/ {
		self::logs()->keepLog($this);

		parent::store();
	}
}
