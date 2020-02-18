<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Class IliasPermission
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class IliasPermission extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "il";
    const ILIAS_PERMISSION_JOIN = 1;
    const ILIAS_PERMISSIONS
        = [
            self::ILIAS_PERMISSION_JOIN => "join"
        ];


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
                return false;

            default:
                return true;
        }
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $ilias_permission = self::ILIAS_PERMISSION_JOIN;


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return htmlspecialchars(self::plugin()->translate("iliaspermission_" . self::ILIAS_PERMISSIONS[$this->ilias_permission], RulesGUI::LANG_MODULE));
    }


    /**
     * @return int
     */
    public function getIliasPermission() : int
    {
        return $this->ilias_permission;
    }


    /**
     * @param int $ilias_permission
     */
    public function setIliasPermission(int $ilias_permission)/* : void*/
    {
        $this->ilias_permission = $ilias_permission;
    }
}
