<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class NoResponsibleUsersAssignedFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned
 */
class NoResponsibleUsersAssignedFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var NoResponsibleUsersAssigned
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, NoResponsibleUsersAssigned $rule)
    {
        parent::__construct($parent, $rule);
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        parent::initFields();

        $this->fields = array_merge($this->fields, []);
    }
}
