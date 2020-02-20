<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class GroupChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class GroupChecker extends AbstractRuleChecker
{

    /**
     * @var Group
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(Group $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return (!empty(self::srUserEnrolment()->enrolmentWorkflow()
            ->rules()
            ->getCheckedRules(AbstractRule::PARENT_CONTEXT_RULE_GROUP, $this->rule->getRuleId(), AbstractRule::TYPE_RULE_GROUP, $user_id, $obj_ref_id, true, $this->request)));
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
