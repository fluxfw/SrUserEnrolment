<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole;

use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class GlobalRoleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class GlobalRoleFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var GlobalRole
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, GlobalRole $rule)
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
            "global_role" => [
                self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_OPTIONS  => ["" => ""] + self::srUserEnrolment()->ruleEnrolment()->getAllRoles(),
                "setTitle"              => $this->txt("rule_type_globalrole")
            ]
        ]);
    }
}
