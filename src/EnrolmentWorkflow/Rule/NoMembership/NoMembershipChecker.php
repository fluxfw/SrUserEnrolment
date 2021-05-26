<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership;

use ilObjCourse;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class NoMembershipChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership
 */
class NoMembershipChecker extends AbstractRuleChecker
{

    /**
     * @var NoMembership
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(NoMembership $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        $obj = self::srUserEnrolment()->getIliasObjectByRefId($obj_ref_id);

        if ($obj instanceof ilObjCourse) {
            if (self::srUserEnrolment()->ruleEnrolment()->isEnrolled($obj->getId(), $user_id)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
