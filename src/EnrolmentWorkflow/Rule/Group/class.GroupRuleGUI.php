<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group;

require_once __DIR__ . "/../../../../vendor/autoload.php";

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class GroupRuleGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRuleGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRulesGUI
 */
class GroupRuleGUI extends RuleGUI
{

    /**
     * @inheritDoc
     */
    protected function ungroup()/*: void*/
    {
        die();
    }
}
