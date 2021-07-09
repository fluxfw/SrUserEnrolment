<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\PositionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class OrgUnitSuperiorFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior
 */
class OrgUnitSuperiorFormGUI extends AbstractRuleFormGUI
{

    use PositionFormGUI;

    /**
     * @var OrgUnitSuperior
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, OrgUnitSuperior $rule)
    {
        parent::__construct($parent, $rule);
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            $this->getPositionFormFields()
        );
    }
}
