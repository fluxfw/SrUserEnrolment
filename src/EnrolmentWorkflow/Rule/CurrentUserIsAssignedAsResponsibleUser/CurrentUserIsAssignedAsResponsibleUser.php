<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class CurrentUserIsAssignedAsResponsibleUser
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CurrentUserIsAssignedAsResponsibleUser extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "cuiaaru";
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $only_next_step = false;


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
            case self::PARENT_CONTEXT_ROLE:
                return false;

            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return "";
    }


    /**
     * @return bool
     */
    public function isOnlyNextStep() : bool
    {
        return $this->only_next_step;
    }


    /**
     * @param bool $only_next_step
     */
    public function setOnlyNextStep(bool $only_next_step)/* : void*/
    {
        $this->only_next_step = $only_next_step;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "only_next_step":
                return ($field_value ? 1 : 0);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "only_next_step":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
