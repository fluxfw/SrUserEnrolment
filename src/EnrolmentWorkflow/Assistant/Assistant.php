<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ActiveRecord;
use arConnector;
use ilDate;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Assistant
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Assistant extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_ass";
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $active = true;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $assistant_user_id;
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
    protected $id;
    /**
     * @var ilDate|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $until = null;
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
     * Assistant constructor
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
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return ilObjUser
     */
    public function getAssistantUser() : ilObjUser
    {
        return new ilObjUser($this->assistant_user_id);
    }


    /**
     * @return int
     */
    public function getAssistantUserId() : int
    {
        return $this->assistant_user_id;
    }


    /**
     * @param int $assistant_user_id
     */
    public function setAssistantUserId(int $assistant_user_id)/* : void*/
    {
        $this->assistant_user_id = $assistant_user_id;
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
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id)/* : void*/
    {
        $this->id = $id;
    }


    /**
     * @return ilDate|null
     */
    public function getUntil()/* : ?ilDate*/
    {
        return $this->until;
    }


    /**
     * @param ilDate|null $until
     */
    public function setUntil(/*?*/ ilDate $until = null)/* : void*/
    {
        $this->until = $until;
    }


    /**
     * @return ilObjUser
     */
    public function getUser() : ilObjUser
    {
        return new ilObjUser($this->user_id);
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
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->active;
    }


    /**
     * @param bool $active
     */
    public function setActive(bool $active)/* : void*/
    {
        $this->active = $active;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "until":
                if ($field_value !== null) {
                    return $field_value->get(IL_CAL_UNIX);
                } else {
                    return parent::sleep($field_name);
                }

            case "active":
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
            case "until":
                if ($field_value !== null) {
                    return new ilDate($field_value, IL_CAL_UNIX);
                } else {
                    return parent::wakeUp($field_name, $field_value);
                }

            case "active":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
