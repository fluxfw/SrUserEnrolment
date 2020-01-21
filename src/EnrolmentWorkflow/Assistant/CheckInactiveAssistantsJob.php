<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilCronJob;
use ilCronJobResult;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class CheckInactiveAssistantsJob
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CheckInactiveAssistantsJob extends ilCronJob
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_check_inactive_assistants";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * CheckInactiveAssistantsJob constructor
     *
     * @param int|null $parent_id
     */
    public function __construct()
    {

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
        return ilSrUserEnrolmentPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("check_inactive_assistants", AssistantsGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("check_inactive_assistants_description", AssistantsGUI::LANG_MODULE);
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
    public function hasFlexibleSchedule() : bool
    {
        return true;
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
    public function run() : ilCronJobResult
    {
        $time = time();

        $result = new ilCronJobResult();

        if (!self::srUserEnrolment()->enrolmentWorkflow()->assistants()->isEnabled()) {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);

            return $result;
        }

        $count_removed_assistants = 0;
        $count_inactived_assistants = 0;

        foreach (self::srUserEnrolment()->enrolmentWorkflow()->assistants()->getAssistants() as $assistant) {
            if (!$assistant->getAssistantUser()->getActive()) {
                self::srUserEnrolment()->enrolmentWorkflow()->assistants()->deleteAssistant($assistant);

                $count_removed_assistants++;

                continue;
            }

            if ($assistant->getUntil() !== null && $assistant->getUntil()->getUnixTime() < $time) {
                $assistant->setActive(false);

                self::srUserEnrolment()->enrolmentWorkflow()->assistants()->storeAssistant($assistant);

                $count_inactived_assistants++;
            }
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage(nl2br(str_replace("\\n", "\n", self::plugin()->translate("check_inactive_assistants_status", AssistantsGUI::LANG_MODULE, [
            $count_removed_assistants,
            $count_inactived_assistants
        ])), false));

        return $result;
    }
}
