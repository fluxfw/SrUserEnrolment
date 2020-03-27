<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ActiveRecord;
use arConnector;
use ilLPStatus;
use ilObjCourse;
use ilObject;
use ilObjectFactory;
use ilObjectGUIFactory;
use ilObjUser;
use ilParticipant;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Member
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Member extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_mem";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TYPE_MEMBER = ilParticipant::MEMBERSHIP_MEMBER;
    const TYPE_TUTOR = ilParticipant::MEMBERSHIP_TUTOR;
    const TYPE_ADMIN = ilParticipant::MEMBERSHIP_ADMIN;
    const TYPE_REQUEST = 4;
    const TYPES_CORE
        = [
            self::TYPE_MEMBER => "member",
            self::TYPE_TUTOR  => "tutor",
            self::TYPE_ADMIN  => "admin"
        ];
    const TYPES
        = self::TYPES_CORE + [
            self::TYPE_REQUEST => "request"
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
    protected $member_id;
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
    protected $usr_id;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $enrollment_time = null;
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   false
     */
    protected $additional_data = [];
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
     * Member constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "additional_data":
                return json_encode($field_value);

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "additional_data":
                return json_decode($field_value, true);

            default:
                return null;
        }
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
        return new ilObjUser($this->usr_id);
    }


    /**
     * @return ilObjUser
     */
    public function getCreatedUser() : ilObjUser
    {
        return new ilObjUser($this->created_user_id);
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
    public function getType() : int
    {
        if (!($this->getObject() instanceof ilObjCourse)) {
            return self::TYPE_REQUEST;
        }

        if ($this->getObject()->getMembersObject()->isAdmin($this->usr_id)) {
            return self::TYPE_ADMIN;
        }

        if ($this->getObject()->getMembersObject()->isTutor($this->usr_id)) {
            return self::TYPE_TUTOR;
        }

        if ($this->getObject()->getMembersObject()->isMember($this->usr_id)) {
            return self::TYPE_MEMBER;
        }

        return self::TYPE_REQUEST;
    }


    /**
     * @return Request|null
     */
    public function getRequest()/*:Request|null*/
    {
        if ($this->getType() !== self::TYPE_REQUEST) {
            return null;
        }

        return end(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($this->obj_ref_id, null, [$this->usr_id])) ?: null;
    }


    /**
     * @param string $key
     * @param mixed  $value
     */
    public function getAdditionalDataValue(string $key)
    {
        return $this->additional_data[$key];
    }


    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setAdditionalDataValue(string $key, $value)/* : void*/
    {
        $this->additional_data[$key] = $value;
    }


    /**
     * @param string $key
     * @param bool   $checked
     */
    public function setAdditionalDataValueCustomChecked(string $key, bool $checked)/*:void*/
    {
        if (is_bool($this->additional_data[$key])) {
            $this->setAdditionalDataValue($key, $checked);
        }
    }


    /**
     * @return int|null
     */
    public function getLpStatus()/* : ?int*/
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return null;
        }

        return intval(ilLPStatus::_lookupStatus($this->getObjId(), $this->usr_id));
    }


    /**
     * @return bool|null
     */
    public function isLpCompleted()/* : ?bool*/
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return null;
        }

        if (!($this->getObject() instanceof ilObjCourse)) {
            return null;
        }

        return $this->getObject()->getMembersObject()->hasPassed($this->usr_id);
    }


    /**
     * @param bool $completed
     */
    public function setLpCompleted(bool $completed)/*:void*/
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return;
        }

        if (!($this->getObject() instanceof ilObjCourse)) {
            return;
        }

        $this->getObject()->getMembersObject()->updatePassed($this->usr_id, $completed, true);
        (new ilObjectGUIFactory())->getInstanceByRefId($this->obj_ref_id)->updateLPFromStatus($this->usr_id, $completed);
    }


    /**
     * @param int $lp_status
     */
    public function setLpStatus(int $lp_status)/*:void*/
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return;
        }

        if (!($this->getObject() instanceof ilObjCourse)) {
            return;
        }

        // TODO
    }


    /**
     * @return int
     */
    public function getMemberId() : int
    {
        return $this->member_id;
    }


    /**
     * @param int $member_id
     */
    public function setMemberId(int $member_id)/* : void*/
    {
        $this->member_id = $member_id;
    }


    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }


    /**
     * @param int $usr_id
     */
    public function setUsrId(int $usr_id)/* : void*/
    {
        $this->usr_id = $usr_id;
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
     * @return int|null
     */
    public function getEnrollmentTime()/* : ?int*/
    {
        return $this->enrollment_time;
    }


    /**
     * @param int|null $enrollment_time
     */
    public function setEnrollmentTime(/*?*/ int $enrollment_time = null)/* : void*/
    {
        $this->enrollment_time = $enrollment_time;
    }


    /**
     * @return array
     */
    public function getAdditionalData() : array
    {
        return $this->additional_data;
    }


    /**
     * @param array $additional_data
     */
    public function setAdditionalData(array $additional_data)/* : void*/
    {
        $this->additional_data = $additional_data;
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
