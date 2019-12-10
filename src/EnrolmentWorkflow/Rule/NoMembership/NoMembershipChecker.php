<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership;

use ilObjCourse;
use ilObjectFactory;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class NoMembershipChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
        $obj = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);

        if ($obj instanceof ilObjCourse) {

            if ($obj->getMembersObject()->isAssigned($user_id)) {

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
