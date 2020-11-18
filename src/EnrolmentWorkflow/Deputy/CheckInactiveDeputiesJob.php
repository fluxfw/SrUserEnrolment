<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ilCronJob;
use ilCronJobResult;
use ilCronManager;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class CheckInactiveDeputiesJob
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CheckInactiveDeputiesJob extends ilCronJob
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CRON_JOB_ID = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_check_inactive_deputies";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * CheckInactiveDeputiesJob constructor
     */
    public function __construct()
    {

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
        return self::plugin()->translate("check_inactive_deputies_description", DeputiesGUI::LANG_MODULE);
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
        return ilSrUserEnrolmentPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("check_inactive_deputies", DeputiesGUI::LANG_MODULE);
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
    public function run() : ilCronJobResult
    {
        $time = time();

        $result = new ilCronJobResult();

        if (!self::srUserEnrolment()->enrolmentWorkflow()->deputies()->isEnabled()) {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);

            return $result;
        }

        $count_removed_deputies = 0;
        $count_inactived_deputies = 0;

        foreach (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getDeputies() as $deputy) {
            if (!$deputy->getDeputyUser()->getActive()) {
                self::srUserEnrolment()->enrolmentWorkflow()->deputies()->deleteDeputy($deputy);

                $count_removed_deputies++;

                continue;
            }

            if ($deputy->getUntil() !== null && $deputy->getUntil()->getUnixTime() < $time) {
                $deputy->setActive(false);

                self::srUserEnrolment()->enrolmentWorkflow()->deputies()->storeDeputy($deputy);

                $count_inactived_deputies++;
            }

            ilCronManager::ping($this->getId());
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage(nl2br(self::plugin()->translate("check_inactive_deputies_status", DeputiesGUI::LANG_MODULE, [
            $count_removed_deputies,
            $count_inactived_deputies
        ]), false));

        return $result;
    }
}
