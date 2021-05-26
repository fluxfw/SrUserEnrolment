<?php

namespace srag\Plugins\SrUserEnrolment\Job;

use ilCronJob;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\CheckInactiveAssistantsJob;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\CheckInactiveDeputiesJob;
use srag\Plugins\SrUserEnrolment\Log\DeleteOldLogsJob;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RuleEnrolmentJob;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\Job
 */
final class Factory
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


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
     * @param string $job_id
     *
     * @return ilCronJob|null
     */
    public function newInstanceById(string $job_id)/*: ?ilCronJob*/
    {
        switch ($job_id) {
            case RuleEnrolmentJob::CRON_JOB_ID:
                return self::srUserEnrolment()->ruleEnrolment()->rules()->factory()->newJobInstance();

            case CheckInactiveAssistantsJob::CRON_JOB_ID:
                return self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newCheckInactiveAssistantsJobInstance();

            case CheckInactiveDeputiesJob::CRON_JOB_ID:
                return self::srUserEnrolment()->enrolmentWorkflow()->deputies()->factory()->newCheckInactiveDeputiesJobInstance();

            case DeleteOldLogsJob::CRON_JOB_ID:
                return self::srUserEnrolment()->logs()->factory()->newDeleteOldLogsJobInstance();

            default:
                return null;
        }
    }


    /**
     * @return ilCronJob[]
     */
    public function newInstances() : array
    {
        return [
            self::srUserEnrolment()->ruleEnrolment()->rules()->factory()->newJobInstance(),
            self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newCheckInactiveAssistantsJobInstance(),
            self::srUserEnrolment()->enrolmentWorkflow()->deputies()->factory()->newCheckInactiveDeputiesJobInstance(),
            self::srUserEnrolment()->logs()->factory()->newDeleteOldLogsJobInstance()
        ];
    }
}
