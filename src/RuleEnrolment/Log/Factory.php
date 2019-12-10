<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Logs;

use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsTableGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;
use Throwable;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Logs
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
     * @param stdClass $data
     *
     * @return Log
     */
    public function fromDB(stdClass $data) : Log
    {
        $log = $this->newInstance()->withLogId($data->log_id)->withObjectId($data->object_id)->withRuleId($data->rule_id)->withUserId($data->user_id)
            ->withDate(new ilDateTime($data->date, IL_CAL_DATETIME))->withStatus($data->status)->withMessage($data->message);

        return $log;
    }


    /**
     * @return Log
     */
    public function newInstance() : Log
    {
        $log = new Log();

        return $log;
    }


    /**
     * @param int         $object_id
     * @param int|null    $user_id
     * @param string|null $rule_id
     *
     * @return Log
     */
    public function newObjectRuleUserInstance(int $object_id, /*?*/ int $user_id = null, /*?*/ string $rule_id = null) : Log
    {
        $log = $this->newInstance()->withObjectId($object_id)->withUserId($user_id)->withRuleId($rule_id);

        return $log;
    }


    /**
     * @param Throwable   $ex
     * @param int         $object_id
     * @param int|null    $user_id
     * @param string|null $rule_id
     *
     * @return Log
     */
    public function newExceptionInstance(Throwable $ex, int $object_id, /*?*/ int $user_id = null,/*?*/ string $rule_id = null) : Log
    {
        $log = $this->newObjectRuleUserInstance($object_id, $user_id, $rule_id)->withStatus(Log::STATUS_NOT_ENROLLED)->withMessage($ex->getMessage());

        return $log;
    }


    /**
     * @param LogsGUI $parent
     * @param string  $cmd
     *
     * @return LogsTableGUI
     */
    public function newTableInstance(LogsGUI $parent, string $cmd = LogsGUI::CMD_LIST_LOGS) : LogsTableGUI
    {
        $table = new LogsTableGUI($parent, $cmd);

        return $table;
    }
}
