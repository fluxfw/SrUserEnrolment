<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;

/**
 * Class IliasPermissionChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class IliasPermissionChecker extends AbstractRuleChecker
{

    /**
     * @var IliasPermission
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(IliasPermission $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        switch ($this->rule->getIliasPermission()) {
            case IliasPermission::ILIAS_PERMISSION_JOIN:
                return self::dic()->access()->checkAccessOfUser($user_id, "join", "join", $obj_ref_id);

            default:
                return false;
        }
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return [];
    }
}
