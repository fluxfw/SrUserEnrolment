<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Class GroupRulesGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRulesGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI
 */
class GroupRulesGUI extends RulesGUI
{

    /**
     * @inheritDoc
     */
    protected function setTabs()/*:void*/
    {
        parent::setTabs();

        self::addTabs(AbstractRule::PARENT_CONTEXT_RULE_GROUP);
    }


    /**
     * @inheritDoc
     */
    public function getRuleGUIClass() : string
    {
        return GroupRuleGUI::class;
    }


    /**
     * @inheritDoc
     */
    protected function createGroupOfRules()/*:void*/
    {
        die();
    }
}
