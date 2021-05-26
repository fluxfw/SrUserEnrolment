<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser;

use ilCheckboxInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class CurrentUserIsAssignedAsResponsibleUserFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser
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

        $this->fields = array_merge($this->fields, [
            "only_next_step" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ]
        ]);
    }
}
