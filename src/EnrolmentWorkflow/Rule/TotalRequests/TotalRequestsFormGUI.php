<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests;

use ilNumberInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class TotalRequestsFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TotalRequestsFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var TotalRequests
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, TotalRequests $rule)
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
            "total_requests" => [
                self::PROPERTY_CLASS => ilNumberInputGUI::class,
                "setTitle"           => $this->txt("rule_type_totalrequests")
            ]
        ]);
    }
}
