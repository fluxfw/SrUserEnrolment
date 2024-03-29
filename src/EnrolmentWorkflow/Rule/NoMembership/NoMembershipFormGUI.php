<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class NoMembershipFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership
 */
class NoMembershipFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var NoMembership
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, NoMembership $rule)
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
