<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor;

use ilOrgUnitPosition;
use ilOrgUnitUserAssignment;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDFChecker;

/**
 * Class UDFSupervisorChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UDFSupervisorChecker extends UDFChecker
{

    /**
     * @var UDFSupervisor
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(UDFSupervisor $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    protected function getUserIds(int $user_id) : array
    {
        $org_id = ilOrgUnitUserAssignment::where([
            "position_id" => ilOrgUnitPosition::CORE_POSITION_EMPLOYEE,
            "user_id"     => $user_id
        ])->getArray(null, "orgu_id");

        if (empty($org_id)) {
            return [];
        }

        return array_unique(ilOrgUnitUserAssignment::where([
            "orgu_id"     => $org_id,
            "position_id" => ilOrgUnitPosition::CORE_POSITION_SUPERIOR
        ], [
            "orgu_id"     => "IN",
            "position_id" => "="
        ])->getArray(null, "user_id"));
    }
}
