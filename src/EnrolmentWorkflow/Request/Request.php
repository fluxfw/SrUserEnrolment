<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ActiveRecord;
use arConnector;
use ilDatePresentation;
use ilDateTime;
use ilObject;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Workflow;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Request
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 */
class Request extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_request";
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $accept_time = null;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $accept_user_id = null;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $accepted = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $create_time;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $create_user_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_ref_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $request_id;
    /**
     * @var int[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $responsible_users = [];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $step_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $user_id;


    /**
     * Request constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @param int $responsible_user_id
     */
    public function addResponsibleUser(int $responsible_user_id) : void
    {
        if (!in_array($responsible_user_id, $this->responsible_users)) {
            $this->responsible_users[] = $responsible_user_id;
        }
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return int
     */
    public function getCreatedTime() : int
    {
        return $this->create_time;
    }


    /**
     * @return ilObjUser
     */
    public function getCreatedUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->create_user_id);
    }


    /**
     * @return int
     */
    public function getCreatedUserId() : int
    {
        return $this->create_user_id;
    }


    /**
     * @return int|null
     */
    public function getEditedTime() : ?int
    {
        return $this->accept_time;
    }


    /**
     * @return ilObjUser
     */
    public function getEditedUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->accept_user_id);
    }


    /**
     * @return int|null
     */
    public function getEditedUserId() : int
    {
        return $this->accept_user_id;
    }


    /**
     * @return string
     */
    public function getFormattedCreatedTime() : string
    {
        return ilDatePresentation::formatDate(new ilDateTime($this->create_time, IL_CAL_UNIX));
    }


    /**
     * @return string
     */
    public function getFormattedEditedTime() : string
    {
        return ilDatePresentation::formatDate(new ilDateTime($this->accept_time, IL_CAL_UNIX));
    }


    /**
     * @return array
     */
    public function getFormattedRequiredData() : array
    {
        return self::srUserEnrolment()
            ->requiredData()
            ->fills()
            ->formatAsStrings(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step_id, $this->getRequiredData());
    }


    /**
     * @return ilObjUser[]
     */
    public function getFormattedResponsibleUsers() : array
    {
        return array_combine($this->responsible_users, array_map(function (int $responsible_user_id) : ilObjUser {
            return self::srUserEnrolment()->getIliasObjectById($responsible_user_id);
        }, $this->responsible_users));
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id) : void
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @param int $obj_ref_id
     */
    public function setObjRefId(int $obj_ref_id) : void
    {
        $this->obj_ref_id = $obj_ref_id;
    }


    /**
     * @return ilObject
     */
    public function getObject() : ilObject
    {
        return self::srUserEnrolment()->getIliasObjectByRefId($this->obj_ref_id);
    }


    /**
     * @return RequestGroup
     */
    public function getRequestGroup() : RequestGroup
    {
        return self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequestGroup($this->obj_ref_id, $this->user_id, $this->request_id);
    }


    /**
     * @return int
     */
    public function getRequestId() : int
    {
        return $this->request_id;
    }


    /**
     * @param int $request_id
     */
    public function setRequestId(int $request_id) : void
    {
        $this->request_id = $request_id;
    }


    /**
     * @param bool $obj_ref_id
     *
     * @return string
     */
    public function getRequestLink(bool $obj_ref_id = false) : string
    {
        return ILIAS_HTTP_PATH . "/goto.php?target=uihk_" . ilSrUserEnrolmentPlugin::PLUGIN_ID . "_req_" . $this->request_id . ($obj_ref_id ? "_" . $this->obj_ref_id : "");
    }


    /**
     * @return array
     */
    public function getRequiredData() : array
    {
        return self::srUserEnrolment()->requiredData()->fills()->getFillValues($this->request_id);
    }


    /**
     * @return int[]
     */
    public function getResponsibleUsers() : array
    {
        return $this->responsible_users;
    }


    /**
     * @param int[] $responsible_users
     */
    public function setResponsibleUsers(array $responsible_users) : void
    {
        $this->responsible_users = array_map("intval", array_values($responsible_users));
    }


    /**
     * @return Step|null
     */
    public function getStep() : ?Step
    {
        return self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($this->step_id);
    }


    /**
     * @return int
     */
    public function getStepId() : int
    {
        return $this->step_id;
    }


    /**
     * @param int $step_id
     */
    public function setStepId(int $step_id) : void
    {
        $this->step_id = $step_id;
    }


    /**
     * @return ilObjUser
     */
    public function getUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->user_id);
    }


    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id) : void
    {
        $this->user_id = $user_id;
    }


    /**
     * @return Workflow|null
     */
    public function getWorkflow() : ?Workflow
    {
        return self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($this->getStep()->getWorkflowId());
    }


    /**
     * @return bool
     */
    public function isEdited() : bool
    {
        return $this->accepted;
    }


    /**
     * @param int $created_time
     */
    public function setCreatedTime(int $created_time) : void
    {
        $this->create_time = $created_time;
    }


    /**
     * @param int $created_user_id
     */
    public function setCreatedUserId(int $created_user_id) : void
    {
        $this->create_user_id = $created_user_id;
    }


    /**
     * @param bool $edited
     */
    public function setEdited(bool $edited) : void
    {
        $this->accepted = $edited;
    }


    /**
     * @param int|null $edited_time
     */
    public function setEditedTime(/*?*/ int $edited_time = null) : void
    {
        $this->accept_time = $edited_time;
    }


    /**
     * @param int|null $edited_user_id
     */
    public function setEditedUserId(/*?*/ int $edited_user_id = null) : void
    {
        $this->accept_user_id = $edited_user_id;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "accepted":
                return ($field_value ? 1 : 0);

            case "responsible_users":
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
            case "accepted":
                return boolval($field_value);

            case "responsible_users":
                return json_decode($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
