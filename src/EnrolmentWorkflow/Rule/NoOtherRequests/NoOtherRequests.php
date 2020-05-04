<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class NoOtherRequests
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoOtherRequests extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "noothrreq";


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
            case self::PARENT_CONTEXT_ROLE:
                return false;

            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return "";
    }
}
