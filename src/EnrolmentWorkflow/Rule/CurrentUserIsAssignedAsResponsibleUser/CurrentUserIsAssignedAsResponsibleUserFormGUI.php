<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class CurrentUserIsAssignedAsResponsibleUserFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CurrentUserIsAssignedAsResponsibleUserFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var CurrentUserIsAssignedAsResponsibleUser
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, CurrentUserIsAssignedAsResponsibleUser $rule)
    {
        parent::__construct($parent, $rule);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge($this->fields, []);
    }
}
