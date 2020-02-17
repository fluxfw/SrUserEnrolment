<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class CurrentUserIsAssignedAsResponsibleUser
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CurrentUserIsAssignedAsResponsibleUser extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "cuiaaru";


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
                return false;

            default:
                return parent::supportsParentContext($parent_context);
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
