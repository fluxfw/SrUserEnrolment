<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class LanguageFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LanguageFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var Language
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, Language $object)
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
