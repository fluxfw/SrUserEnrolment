<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\PositionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class OrgUnitSuperiorFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitSuperiorFormGUI extends AbstractRuleFormGUI
{

    use PositionFormGUI;
    /**
     * @var OrgUnitSuperior
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, OrgUnitSuperior $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            $this->getPositionFormFields()
        );
    }
}
