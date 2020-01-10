<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class NoResponsibleUsersAssigned
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoResponsibleUsersAssigned extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "norspusas";


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_ACTION:
                return true;

            default:
                return false;
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
