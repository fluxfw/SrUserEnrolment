<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class NoResponsibleUsersAssignedFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoResponsibleUsersAssignedFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var NoResponsibleUsersAssigned
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, NoResponsibleUsersAssigned $object)
    {
        parent::__construct($parent, $object);
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
