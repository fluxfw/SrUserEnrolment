<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior;

use ilObjOrgUnitTree;
use ilOrgUnitUserAssignment;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;
use stdClass;

/**
 * Class OrgUnitSuperiorChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior
 */
class OrgUnitSuperiorChecker extends AbstractRuleChecker
{

    /**
     * @var OrgUnitSuperior
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(OrgUnitSuperior $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        foreach (
            ilOrgUnitUserAssignment::where([
                "user_id" => $user_id
            ])->getArray(null, "orgu_id") as $org_unit_ref_id
        ) {
            if (!empty(ilOrgUnitUserAssignment::where([
                "orgu_id"     => [$org_unit_ref_id, ilObjOrgUnitTree::_getInstance()->getParent($org_unit_ref_id)],
                "position_id" => $this->rule->getPosition(),
                "user_id"     => self::dic()->user()->getId()
            ])->count())
            ) {
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
        return array_map(function (int $user_id) : stdClass {
            return (object) [
                "obj_ref_id" => $this->rule->getParentId(),
                "user_id"    => $user_id
            ];
        }, array_keys(self::srUserEnrolment()->ruleEnrolment()->getUsers()));
    }
}
