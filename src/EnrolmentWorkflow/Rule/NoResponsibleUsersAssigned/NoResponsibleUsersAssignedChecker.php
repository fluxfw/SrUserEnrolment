<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class NoResponsibleUsersAssignedChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NoResponsibleUsersAssignedChecker extends AbstractRuleChecker
{

    /**
     * @var NoResponsibleUsersAssigned
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(NoResponsibleUsersAssigned $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return ($this->request !== null && empty($this->request->getResponsibleUsers()));
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
