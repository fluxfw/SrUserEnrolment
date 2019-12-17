<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Log;

use ActiveRecord;
use arConnector;
use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Log
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Log
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Log extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const TABLE_NAME = "srusrenr_log";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const STATUS_ENROLLED = 100;
    const STATUS_ENROLL_FAILED = 200;
    const STATUS_USER_CREATED = 300;
    const STATUS_USER_UPDATED = 400;
    const STATUS_USER_FAILED = 500;


    /**
     * @return string
     */
    public final function getConnectorContainerName() : string
    {
        return static::TABLE_NAME;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public final static function returnDbTableName() : string
    {
        return static::TABLE_NAME;
    }


    /**
     * @var array
     */
    public static $status_create_or_update_users
        = [
            self::STATUS_USER_CREATED => self::STATUS_USER_CREATED,
            self::STATUS_USER_UPDATED => self::STATUS_USER_UPDATED,
            self::STATUS_USER_FAILED   => self::STATUS_USER_FAILED
        ];
    /**
     * @var array
     */
    public static $status_enroll
        = [
            self::STATUS_ENROLLED     => self::STATUS_ENROLLED,
            self::STATUS_ENROLL_FAILED => self::STATUS_ENROLL_FAILED
        ];
    /**
     * @var array
     */
    public static $status_all
        = [
            self::STATUS_USER_CREATED  => self::STATUS_USER_CREATED,
            self::STATUS_USER_UPDATED  => self::STATUS_USER_UPDATED,
            self::STATUS_ENROLLED      => self::STATUS_ENROLLED,
            self::STATUS_USER_FAILED   => self::STATUS_USER_FAILED,
            self::STATUS_ENROLL_FAILED => self::STATUS_ENROLL_FAILED
        ];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $log_id = 0;
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
     * @var string|null
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   false
     */
    protected $rule_id = null;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $user_id = null;
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
    public final function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        //parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return int
     */
    public function getLogId() : int
    {
        return $this->log_id;
    }


    /**
     * @param int $log_id
     *
     * @return self
     */
    public function withLogId(int $log_id) : self
    {
        $this->log_id = $log_id;

        return $this;
    }


    /**
     * @return int
     */
    public function getObjectId() : int
    {
        return $this->object_id;
    }


    /**
     * @param int $object_id
     *
     * @return self
     */
    public function withObjectId(int $object_id) : self
    {
        $this->object_id = $object_id;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getRuleId()/* : ?string*/
    {
        return $this->rule_id;
    }


    /**
     * @param string|null $rule_id
     *
     * @return self
     */
    public function withRuleId(/*?*/ string $rule_id = null) : self
    {
        $this->rule_id = $rule_id;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getUserId()/*: ?int*/
    {
        return $this->user_id;
    }


    /**
     * @param int|null $user_id
     *
     * @return self
     */
    public function withUserId(/*?*/ int $user_id = null) : self
    {
        $this->user_id = $user_id;

        return $this;
    }


    /**
     * @return ilDateTime
     */
    public function getDate() : ilDateTime
    {
        return $this->date;
    }


    /**
     * @param ilDateTime $date
     *
     * @return self
     */
    public function withDate(ilDateTime $date) : self
    {
        $this->date = $date;

        return $this;
    }


    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }


    /**
     * @param int $status
     *
     * @return self
     */
    public function withStatus(int $status) : self
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }


    /**
     * @param string $message
     *
     * @return self
     */
    public function withMessage(string $message) : self
    {
        $this->message = $message;

        return $this;
    }
}
