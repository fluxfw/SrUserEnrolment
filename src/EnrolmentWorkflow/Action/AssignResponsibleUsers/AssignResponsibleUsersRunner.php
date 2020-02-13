<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers;

use ilOrgUnitUserAssignment;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class AssignResponsibleUsersRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssignResponsibleUsersRunner extends AbstractActionRunner
{

    /**
     * @var AssignResponsibleUsers
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(AssignResponsibleUsers $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request)/*:void*/
    {
        switch ($this->action->getUsersType()) {
            case AssignResponsibleUsers::USER_TYPE_POSITION:
                $responsible_users = [];

                foreach (
                    ilOrgUnitUserAssignment::where([
                        "user_id" => $request->getUserId()
                    ])->getArray(null, "orgu_id") as $org_unit_ref_id
                ) {
                    $responsible_users = array_unique(array_merge($responsible_users, ilOrgUnitUserAssignment::where([
                        "orgu_id"     => $org_unit_ref_id,
                        "position_id" => $this->action->getAssignPositions()
                    ])->getArray(null, "user_id")));
                }
                break;

            case AssignResponsibleUsers::USER_TYPE_SPECIFIC_USERS:
                $responsible_users = $this->action->getSpecificUsers();
                break;

            default:
                $responsible_users = [];
                break;
        }

        $responsible_users = array_filter($responsible_users, function (int $user_id) use ($request): bool {
            return (count(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($request->getObjRefId(), null, $user_id)) < 2);
        });

        $request->setResponsibleUsers($responsible_users);
    }
}
