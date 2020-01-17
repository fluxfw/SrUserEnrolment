<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field\FieldFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value\ValueFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class UDFFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UDFFormGUI extends AbstractRuleFormGUI
{

    use FieldFormGUI;
    use OperatorFormGUI;
    use ValueFormGUI;
    /**
     * @var UDF
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, UDF $rule)
    {
        parent::__construct($parent, $rule);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            $this->getFieldFormFields(),
            $this->getOperatorFormFields1(),
            $this->getValueFormFields(),
            $this->getOperatorFormFields2()
        );
    }
}
