<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Always;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class AlwaysFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Always
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AlwaysFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var Always
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, Always $object)
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
