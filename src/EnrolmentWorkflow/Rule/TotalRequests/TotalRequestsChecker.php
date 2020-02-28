<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class TotalRequestsChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TotalRequestsChecker extends AbstractRuleChecker
{

    /**
     * @var TotalRequests
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(TotalRequests $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return (count(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($obj_ref_id, null, [$user_id])) >= $this->rule->getTotalRequests());
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
