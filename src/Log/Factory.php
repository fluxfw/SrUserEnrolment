<?php

namespace srag\Plugins\SrUserEnrolment\Logs;

use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;
use Throwable;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\Logs
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

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
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @param Throwable $ex
     * @param int       $object_id
     * @param int       $rule_id
     * @param int|null  $user_id
     *
     * @return Log
     */
    public function exceptionLog(Throwable $ex, int $object_id, int $rule_id, /*?*/ int $user_id = null) : Log
    {
        $log = $this->objectRuleUserLog($object_id, $rule_id, $user_id)->withStatus(Log::STATUS_NOT_ENROLLED)->withMessage($ex->getMessage());

        return $log;
    }


    /**
     * @param stdClass $data
     *
     * @return Log
     */
    public function fromDB(stdClass $data) : Log
    {
        $log = $this->log()->withLogId($data->log_id)->withObjectId($data->object_id)->withRuleId($data->rule_id)->withUserId($data->user_id)
            ->withDate(new ilDateTime($data->date, IL_CAL_DATETIME))->withStatus($data->status)->withMessage($data->message);

        return $log;
    }


    /**
     * @return Log
     */
    public function log() : Log
    {
        $log = new Log();

        return $log;
    }


    /**
     * @param int      $object_id
     * @param int      $rule_id
     * @param int|null $user_id
     *
     * @return Log
     */
    public function objectRuleUserLog(int $object_id, int $rule_id, /*?*/ int $user_id = null) : Log
    {
        $log = $this->log()->withObjectId($object_id)->withRuleId($rule_id)->withUserId($user_id);

        return $log;
    }
}
