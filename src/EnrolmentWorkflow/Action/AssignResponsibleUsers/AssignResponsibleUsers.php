<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionsGUI;

/**
 * Class AssignResponsibleUsers
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssignResponsibleUsers extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "aru";
    const USER_TYPE_POSITION = 1;
    const USER_TYPE_SPECIFIC_USERS = 2;
    const USER_TYPES
        = [
            self::USER_TYPE_POSITION       => "position",
            self::USER_TYPE_SPECIFIC_USERS => "specific_users"
        ];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $users_type = self::USER_TYPE_POSITION;
    /**
     * @var int[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $assign_positions = [];
    /**
     * @var int[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $specific_users = [];


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        $descriptions = [];

        $descriptions[] = self::plugin()->translate("userstype_" . self::USER_TYPES[$this->users_type], ActionsGUI::LANG_MODULE);

        switch ($this->users_type) {
            case self::USER_TYPE_POSITION:
                $descriptions = array_merge($descriptions, array_filter(array_keys(self::srUserEnrolment()->ruleEnrolment()->getPositions()), function (int $position_id) : bool {
                    return in_array($position_id, $this->assign_positions);
                }, ARRAY_FILTER_USE_KEY));
                break;

            case self::USER_TYPE_SPECIFIC_USERS:
                $descriptions = array_merge($descriptions, array_filter(array_keys(self::srUserEnrolment()->ruleEnrolment()->getUsers()), function (int $user_id) : bool {
                    return in_array($user_id, $this->specific_users);
                }, ARRAY_FILTER_USE_KEY));
                break;

            default:
                break;
        }

        return nl2br(implode("\n", $descriptions), false);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "assign_positions":
            case "specific_users":
                return json_encode($field_value);

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
            case "assign_positions":
            case "specific_users":
                return json_decode($field_value, true);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }


    /**
     * @return int
     */
    public function getUsersType() : int
    {
        return $this->users_type;
    }


    /**
     * @param int $users_type
     */
    public function setUsersType(int $users_type)/* : void*/
    {
        $this->users_type = $users_type;
    }


    /**
     * @return int[]
     */
    public function getAssignPositions() : array
    {
        return $this->assign_positions;
    }


    /**
     * @param int[] $assign_positions
     */
    public function setAssignPositions(array $assign_positions)/* : void*/
    {
        $this->assign_positions = $assign_positions;
    }


    /**
     * @return int[]
     */
    public function getSpecificUsers() : array
    {
        return $this->specific_users;
    }


    /**
     * @param int[] $specific_users
     */
    public function setSpecificUsers(array $specific_users)/* : void*/
    {
        $this->specific_users = $specific_users;
    }
}
