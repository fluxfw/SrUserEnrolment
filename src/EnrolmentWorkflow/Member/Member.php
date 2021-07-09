<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ActiveRecord;
use arConnector;
use ilLPStatus;
use ilObjCourse;
use ilObject;
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
 */
class Member extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_mem";
    const TYPES
        = self::TYPES_CORE + [
            self::TYPE_REQUEST => "request"
        ];
    const TYPES_CORE
        = [
            self::TYPE_MEMBER => "member",
            self::TYPE_TUTOR  => "tutor",
            self::TYPE_ADMIN  => "admin"
        ];
    const TYPE_ADMIN = ilParticipant::MEMBERSHIP_ADMIN;
    const TYPE_MEMBER = ilParticipant::MEMBERSHIP_MEMBER;
    const TYPE_REQUEST = 4;
    const TYPE_TUTOR = ilParticipant::MEMBERSHIP_TUTOR;
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
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $enrollment_time = null;
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
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $usr_id;


    /**
     * Member constructor
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
     * @return array
     */
    public function getAdditionalData() : array
    {
        return $this->additional_data;
    }


    /**
     * @param array $additional_data
     */
    public function setAdditionalData(array $additional_data) : void
    {
        $this->additional_data = $additional_data;
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
        return $this->created_time;
    }


    /**
     * @param int $created_time
     */
    public function setCreatedTime(int $created_time) : void
    {
        $this->created_time = $created_time;
    }


    /**
     * @return ilObjUser
     */
    public function getCreatedUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->created_user_id);
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
    public function setCreatedUserId(int $created_user_id) : void
    {
        $this->created_user_id = $created_user_id;
    }


    /**
     * @return int|null
     */
    public function getEnrollmentTime() : ?int
    {
        return $this->enrollment_time;
    }


    /**
     * @param int|null $enrollment_time
     */
    public function setEnrollmentTime(/*?*/ int $enrollment_time = null) : void
    {
        $this->enrollment_time = $enrollment_time;
    }


    /**
     * @return int|null
     */
    public function getLpStatus() : ?int
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return null;
        }

        return intval(ilLPStatus::_lookupStatus($this->obj_id, $this->usr_id));
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
    public function setMemberId(int $member_id) : void
    {
        $this->member_id = $member_id;
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
     * @return Request|null
     */
    public function getRequest() : ?Request
    {
        if ($this->getType() !== self::TYPE_REQUEST) {
            return null;
        }

        $requests = self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($this->obj_ref_id, null, [$this->usr_id]);

        return (end($requests) ?: null);
    }


    /**
     * @return int
     */
    public function getType() : int
    {
        if (!($this->getObject() instanceof ilObjCourse)) {
            return self::TYPE_REQUEST;
        }

        $entrolled_type = self::srUserEnrolment()->ruleEnrolment()->getEnrolledType($this->obj_id, $this->usr_id);
        if ($entrolled_type !== null) {
            return $entrolled_type;
        }

        return self::TYPE_REQUEST;
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
    public function setUpdatedTime(int $updated_time) : void
    {
        $this->updated_time = $updated_time;
    }


    /**
     * @return ilObjUser
     */
    public function getUpdatedUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->updated_user_id);
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
    public function setUpdatedUserId(int $updated_user_id) : void
    {
        $this->updated_user_id = $updated_user_id;
    }


    /**
     * @return ilObjUser
     */
    public function getUser() : ilObjUser
    {
        return self::srUserEnrolment()->getIliasObjectById($this->usr_id);
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
    public function setUsrId(int $usr_id) : void
    {
        $this->usr_id = $usr_id;
    }


    /**
     * @return bool|null
     */
    public function isLpCompleted() : ?bool
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
     * @param string $key
     * @param mixed  $value
     */
    public function setAdditionalDataValue(string $key, $value) : void
    {
        $this->additional_data[$key] = $value;
    }


    /**
     * @param string $key
     * @param bool   $checked
     */
    public function setAdditionalDataValueCustomChecked(string $key, bool $checked) : void
    {
        if (is_bool($this->additional_data[$key])) {
            $this->setAdditionalDataValue($key, $checked);
        }
    }


    /**
     * @param bool $completed
     */
    public function setLpCompleted(bool $completed) : void
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
    public function setLpStatus(int $lp_status) : void
    {
        if ($this->getType() === self::TYPE_REQUEST) {
            return;
        }

        if (!($this->getObject() instanceof ilObjCourse)) {
            return;
        }

        ilLPStatus::writeStatus($this->obj_id, $this->usr_id, $lp_status);
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
                return parent::sleep($field_name);
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
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
