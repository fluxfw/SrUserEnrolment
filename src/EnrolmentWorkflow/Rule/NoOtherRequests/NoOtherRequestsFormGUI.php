<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class NoOtherRequestsFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoOtherRequestsFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var NoOtherRequests
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, NoOtherRequests $object)
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
