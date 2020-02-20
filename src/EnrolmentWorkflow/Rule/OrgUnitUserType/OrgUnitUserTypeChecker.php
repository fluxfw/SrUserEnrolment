<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType;

use ilDBConstants;
use ilOrgUnitUserAssignment;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorChecker;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorConstants;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\PositionConstants;
use stdClass;

/**
 * Class OrgUnitUserTypeChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitUserTypeChecker extends AbstractRuleChecker
{

    use OperatorChecker;
    /**
     * @var OrgUnitUserType
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(OrgUnitUserType $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        switch ($this->rule->getOrgUnitUserType()) {
            case OrgUnitUserType::ORG_UNIT_USER_TYPE_TITLE:
                if (!$this->checkOperator(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($obj_ref_id)), $this->rule->getTitle(), $this->rule->getOperator(),
                    $this->rule->isOperatorNegated(), $this->rule->isOperatorCaseSensitive())
                ) {
                    return false;
                }
                break;

            case OrgUnitUserType::ORG_UNIT_USER_TYPE_TREE:
                switch ($this->rule->getOperator()) {
                    case OperatorConstants::OPERATOR_EQUALS:
                        if ($obj_ref_id !== $this->rule->getRefId()) {
                            return false;
                        }
                        break;

                    case OperatorConstants::OPERATOR_EQUALS_SUBSEQUENT:
                        if ($obj_ref_id !== $this->rule->getRefId()) {
                            if (!in_array($obj_ref_id, self::dic()->tree()->getSubTree(self::dic()->tree()->getNodeData($this->rule->getRefId()), false))) {
                                return false;
                            }
                        }
                        break;

                    default:
                        return false;
                }
                break;

            default:
                return false;
        }

        if ($this->rule->getPosition() !== PositionConstants::POSITION_ALL) {
            if (ilOrgUnitUserAssignment::where([
                    "user_id"     => $user_id,
                    "orgu_id"     => $obj_ref_id,
                    "position_id" => $this->rule->getPosition()
                ])->first() === null
            ) {
                return false;
            }
        }

        return true;
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        $wheres = ["type=%s"];
        $types = [ilDBConstants::T_TEXT];
        $values = ["orgu"];

        return self::dic()->database()->fetchAllCallback(self::srUserEnrolment()->ruleEnrolment()
            ->getObjectFilterStatement($wheres, $types, $values, ["ref_id", "user_id"],
                'INNER JOIN ' . ilOrgUnitUserAssignment::returnDbTableName() . ' ON object_reference.ref_id=' . ilOrgUnitUserAssignment::returnDbTableName() . '.orgu_id'),
            function (stdClass $data) : stdClass {
                return (object) ["obj_ref_id" => $data->ref_id, "user_id" => $data->user_id];
            });
    }
}
