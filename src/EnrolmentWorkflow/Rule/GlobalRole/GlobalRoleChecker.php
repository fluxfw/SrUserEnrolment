<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;
use stdClass;

/**
 * Class GlobalRoleChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole
 */
class GlobalRoleChecker extends AbstractRuleChecker
{

    /**
     * @var GlobalRole
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(GlobalRole $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return self::srUserEnrolment()->ruleEnrolment()->isEnrolled($this->rule->getGlobalRole(), $user_id);
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return array_map(function (int $user_id) : stdClass {
            return (object) [
                "obj_ref_id" => $this->rule->getParentId(),
                "user_id"    => $user_id
            ];
        }, array_keys(self::srUserEnrolment()->ruleEnrolment()->getUsers()));
    }
}
