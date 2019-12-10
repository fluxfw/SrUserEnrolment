<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Always;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class Always
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Always
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Always extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "alwys";


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return "";
    }
}
