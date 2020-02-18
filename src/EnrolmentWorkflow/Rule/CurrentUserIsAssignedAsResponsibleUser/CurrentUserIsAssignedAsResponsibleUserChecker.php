<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class CurrentUserIsAssignedAsResponsibleUserChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CurrentUserIsAssignedAsResponsibleUserChecker extends AbstractRuleChecker
{

    /**
     * @var CurrentUserIsAssignedAsResponsibleUser
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(CurrentUserIsAssignedAsResponsibleUser $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return ($this->request !== null
            && ($this->rule->isOnlyNextStep() ? $this->rule->getParentContext() === AbstractRule::PARENT_CONTEXT_STEP
                && (self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($this->rule->getParentId())->getSort() - self::srUserEnrolment()
                        ->enrolmentWorkflow()
                        ->steps()
                        ->getStepById($this->request->getStepId())
                        ->getSort() === 10) : false)
            && in_array($user_id, $this->request->getResponsibleUsers()));
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
