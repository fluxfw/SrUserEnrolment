<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class Language
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Language extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "lang";


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return "";
    }
}
