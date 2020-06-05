<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers;

use ilObjUser;
use ilOrgUnitUserAssignment;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorChecker;

/**
 * Class AssignResponsibleUsersRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssignResponsibleUsersRunner extends AbstractActionRunner
{

    use OperatorChecker;

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
                    if ($this->action->isAssignPositionsRecursive()) {
                        $org_unit_ref_id = array_slice(array_map(function (array $child) : int {
                            return $child["child"];
                        }, self::dic()->repositoryTree()->getPathFull($org_unit_ref_id)), 3);
                    }

                    $responsible_users = array_unique(array_merge($responsible_users, ilOrgUnitUserAssignment::where([
                        "orgu_id"     => $org_unit_ref_id,
                        "position_id" => $this->action->getAssignPositions()
                    ])->getArray(null, "user_id")));
                }

                if (!empty($this->action->getAssignPositionsUdf())) {
                    $responsible_users = array_filter($responsible_users, function (int $user_id) : bool {
                        return (count($this->action->getAssignPositionsUdf()) === count(array_filter($this->action->getAssignPositionsUdf(), function (array $field) use ($user_id) : bool {
                                $user = new ilObjUser($user_id);

                                $udf_values = $user->getUserDefinedData();

                                $field_id = self::srUserEnrolment()->excelImport()->getUserDefinedFieldID($field["field"]);
                                if (empty($field_id) || empty($udf_value = strval($udf_values[($field_id = "f_" . $field_id)]))) {
                                    return false;
                                }

                                return $this->checkOperator($udf_value, $field["value"], intval($field["operator"]), boolval($field["operator_negated"]), boolval($field["operator_case_sensitive"]));
                            })));
                    });
                }
                break;

            case AssignResponsibleUsers::USER_TYPE_SPECIFIC_USERS:
                $responsible_users = $this->action->getSpecificUsers();
                break;

            case AssignResponsibleUsers::USER_TYPE_GLOBAL_ROLES:
                $responsible_users = [];

                foreach ($this->action->getGlobalRoles() as $role_id) {

                    $responsible_users = array_merge($responsible_users, self::srUserEnrolment()->ruleEnrolment()->getEnrolleds($role_id));
                }
                break;

            default:
                $responsible_users = [];
                break;
        }

        $responsible_users = array_filter($responsible_users, function (int $user_id) use ($request): bool {
            return ($request->getUserId() !== $user_id && count(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($request->getObjRefId(), null, [$user_id])) < 2);
        });

        $request->setResponsibleUsers($responsible_users);
    }
}
