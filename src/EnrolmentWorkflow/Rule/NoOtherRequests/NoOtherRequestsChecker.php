<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class NoOtherRequestsChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoOtherRequestsChecker extends AbstractRuleChecker
{

    /**
     * @var NoOtherRequests
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(NoOtherRequests $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return (count(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($obj_ref_id, null, [$user_id])) < 2);
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
