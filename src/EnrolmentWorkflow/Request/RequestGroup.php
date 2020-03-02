<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ActiveRecord;
use arConnector;
use ilDatePresentation;
use ilDateTime;
use ilObject;
use ilObjectFactory;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestGroup
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RequestGroup extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const TABLE_NAME = "srusrenr_req_grp";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const EDITED_STATUS_NOT_EDITED = 1;
    const EDITED_STATUS_IN_EDITING = 2;
    const EDITED_STATUS_ACCEPTED = 3;
    const EDITED_STATUS_NOT_ACCEPTED = 4;
    const EDITED_STATUS
        = [
            self::EDITED_STATUS_NOT_EDITED   => "not_edited",
            self::EDITED_STATUS_IN_EDITING   => "in_editing",
            self::EDITED_STATUS_ACCEPTED     => "accepted",
            self::EDITED_STATUS_NOT_ACCEPTED => "not_accepted"
        ];
    const EDITED_STATUS_ICON
        = [
            self::EDITED_STATUS_NOT_EDITED   => "scorm/not_attempted.svg",
            self::EDITED_STATUS_IN_EDITING   => "scorm/incomplete.svg",
            self::EDITED_STATUS_ACCEPTED     => "scorm/completed.svg",
            self::EDITED_STATUS_NOT_ACCEPTED => "scorm/failed.svg"
        ];


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $request_group_id;
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
    protected $user_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $current_request_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       2
     * @con_is_notnull   true
     */
    protected $edited_status = self::EDITED_STATUS_NOT_EDITED;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $created_time;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $created_user_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $updated_time;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $updated_user_id;


    /**
     * RequestGroup constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return ilObject
     */
    public function getObject() : ilObject
    {
        return ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);
    }


    /**
     * @return ilObjUser
     */
    public function getUser() : ilObjUser
    {
        return new ilObjUser($this->user_id);
    }


    /**
     * @return Request|null
     */
    public function getCurrentRequest()/*:?Request*/
    {
        return self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequestById($this->current_request_id);
    }


    /**
     * @return string
     */
    public function getFormattedCreatedTime() : string
    {
        return ilDatePresentation::formatDate(new ilDateTime($this->created_time, IL_CAL_UNIX));
    }


    /**
     * @return ilObjUser
     */
    public function getCreatedUser() : ilObjUser
    {
        return new ilObjUser($this->created_user_id);
    }


    /**
     * @return string
     */
    public function getFormattedUpdatedTime() : string
    {
        return ilDatePresentation::formatDate(new ilDateTime($this->updated_time, IL_CAL_UNIX));
    }


    /**
     * @return ilObjUser
     */
    public function getUpdatedUser() : ilObjUser
    {
        return new ilObjUser($this->updated_user_id);
    }


    /**
     * @return int
     */
    public function getRequestGroupId() : int
    {
        return $this->request_group_id;
    }


    /**
     * @param int $request_group_id
     */
    public function setRequestGroupId(int $request_group_id)/* : void*/
    {
        $this->request_group_id = $request_group_id;
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
    public function setObjRefId(int $obj_ref_id)/* : void*/
    {
        $this->obj_ref_id = $obj_ref_id;
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
    public function setObjId(int $obj_id)/* : void*/
    {
        $this->obj_id = $obj_id;
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
    public function setUserId(int $user_id)/* : void*/
    {
        $this->user_id = $user_id;
    }


    /**
     * @return int
     */
    public function getCurrentRequestId() : int
    {
        return $this->current_request_id;
    }


    /**
     * @param int $current_request_id
     */
    public function setCurrentRequestId(int $current_request_id)/* : void*/
    {
        $this->current_request_id = $current_request_id;
    }


    /**
     * @return int
     */
    public function getEditedStatus() : int
    {
        return $this->edited_status;
    }


    /**
     * @param int $edited_status
     */
    public function setEditedStatus(int $edited_status)/* : void*/
    {
        $this->edited_status = $edited_status;
    }


    /**
     * @return int
     */
    public function getCreatedTime() : int
    {
        return $this->created_time;
    }


    /**
     * @param int $created_time
     */
    public function setCreatedTime(int $created_time)/* : void*/
    {
        $this->created_time = $created_time;
    }


    /**
     * @return int
     */
    public function getCreatedUserId() : int
    {
        return $this->created_user_id;
    }


    /**
     * @param int $created_user_id
     */
    public function setCreatedUserId(int $created_user_id)/* : void*/
    {
        $this->created_user_id = $created_user_id;
    }


    /**
     * @return int
     */
    public function getUpdatedTime() : int
    {
        return $this->updated_time;
    }


    /**
     * @param int $updated_time
     */
    public function setUpdatedTime(int $updated_time)/* : void*/
    {
        $this->updated_time = $updated_time;
    }


    /**
     * @return int
     */
    public function getUpdatedUserId() : int
    {
        return $this->updated_user_id;
    }


    /**
     * @param int $updated_user_id
     */
    public function setUpdatedUserId(int $updated_user_id)/* : void*/
    {
        $this->updated_user_id = $updated_user_id;
    }
}
