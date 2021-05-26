<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group;

require_once __DIR__ . "/../../../../vendor/autoload.php";

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Class GroupRulesGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRulesGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI
 */
class GroupRulesGUI extends RulesGUI
{

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


    /**
     * @inheritDoc
     */
    protected function setTabs()/*:void*/
    {
        parent::setTabs();

        self::addTabs(AbstractRule::PARENT_CONTEXT_RULE_GROUP);
    }
}
