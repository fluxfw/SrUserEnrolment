<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class GlobalRole
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class GlobalRole extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "glblrol";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $global_role = 0;


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return strval(self::srUserEnrolment()->ruleEnrolment()->getAllRoles()[$this->global_role]);
    }


    /**
     * @return int
     */
    public function getGlobalRole() : int
    {
        return $this->global_role;
    }


    /**
     * @param int $global_role
     */
    public function setGlobalRole(int $global_role)/* : void*/
    {
        $this->global_role = $global_role;
    }
}