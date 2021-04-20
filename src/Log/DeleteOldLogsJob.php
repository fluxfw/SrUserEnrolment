<?php

namespace srag\Plugins\SrUserEnrolment\Log;

use ilCronJob;
use ilCronJobResult;
use ilNumberInputGUI;
use ilPropertyFormGUI;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class DeleteOldLogsJob
 *
 * @package srag\Plugins\SrUserEnrolment\Log
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldLogsJob extends ilCronJob
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_delete_old_logs";
    const KEY_KEEP_OLD_LOGS_TIME = "keep_old_logs_time";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * DeleteOldLogsJob constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function addCustomSettingsToForm(ilPropertyFormGUI $a_form)/*:void*/
    {
        $keep_old_logs_time = new ilNumberInputGUI(self::plugin()->translate(self::KEY_KEEP_OLD_LOGS_TIME, LogsGUI::LANG_MODULE), self::KEY_KEEP_OLD_LOGS_TIME);
        $keep_old_logs_time->setInfo(nl2br(self::plugin()->translate(self::KEY_KEEP_OLD_LOGS_TIME . "_info", LogsGUI::LANG_MODULE), false));
        $keep_old_logs_time->setSuffix(self::plugin()->translate("days", LogsGUI::LANG_MODULE));
        $keep_old_logs_time->setValue(self::srUserEnrolment()->config()->getValue(self::KEY_KEEP_OLD_LOGS_TIME));
        $a_form->addItem($keep_old_logs_time);
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType() : int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleValue()/*:?int*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("delete_old_logs_description", LogsGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @inheritDoc
     */
    public function getTitle() : string
    {
        return ilSrUserEnrolmentPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("delete_old_logs", LogsGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasCustomSettings() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $keep_old_logs_time = self::srUserEnrolment()->config()->getValue(self::KEY_KEEP_OLD_LOGS_TIME);

        if (empty($keep_old_logs_time)) {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);

            return $result;
        }

        $count = self::srUserEnrolment()->logs()->deleteOldLogs($keep_old_logs_time);

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage(self::plugin()->translate("delete_old_logs_status", LogsGUI::LANG_MODULE, [$count]));

        return $result;
    }


    /**
     * @inheritDoc
     */
    public function saveCustomSettings(ilPropertyFormGUI $a_form) : bool
    {
        self::srUserEnrolment()->config()->setValue(self::KEY_KEEP_OLD_LOGS_TIME, intval($a_form->getInput(self::KEY_KEEP_OLD_LOGS_TIME)));

        return true;
    }
}
