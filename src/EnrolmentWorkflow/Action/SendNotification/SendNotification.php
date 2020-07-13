<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionsGUI;

/**
 * Class SendNotification
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SendNotification extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "not";
    const TO_TYPES
        = [
            self::TO_TYPE_REQUESTOR                      => "requestor",
            self::TO_TYPE_RESPONSIBLE_USERS              => "responsible_users",
            self::TO_TYPE_SPECIFIC_USERS                 => "specific_users",
            self::TO_TYPE_RESPONSIBLE_USERS_AND_DEPUTIES => "responsible_users_and_deputies"
        ];
    const TO_TYPE_REQUESTOR = 1;
    const TO_TYPE_RESPONSIBLE_USERS = 2;
    const TO_TYPE_RESPONSIBLE_USERS_AND_DEPUTIES = 4;
    const TO_TYPE_SPECIFIC_USERS = 3;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $notification_name = "";
    /**
     * @var int[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $to_specific_users = [];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $to_type = self::TO_TYPE_REQUESTOR;


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        $descriptions = [];

        $notification = self::srUserEnrolment()->notifications4plugin()->notifications()->getNotificationByName($this->notification_name);
        if ($notification !== null) {
            $descriptions[] = $notification->getTitle();
        }

        $descriptions[] = self::plugin()->translate("totype_" . self::TO_TYPES[$this->to_type], ActionsGUI::LANG_MODULE);

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
    }


    /**
     * @return string
     */
    public function getNotificationName() : string
    {
        return $this->notification_name;
    }


    /**
     * @param string $notification_name
     */
    public function setNotificationName(string $notification_name)/* : void*/
    {
        $this->notification_name = $notification_name;
    }


    /**
     * @return int[]
     */
    public function getToSpecificUsers() : array
    {
        return $this->to_specific_users;
    }


    /**
     * @param int[] $to_specific_users
     */
    public function setToSpecificUsers(array $to_specific_users)/* : void*/
    {
        $this->to_specific_users = $to_specific_users;
    }


    /**
     * @return int
     */
    public function getToType() : int
    {
        return $this->to_type;
    }


    /**
     * @param int $to_type
     */
    public function setToType(int $to_type)/* : void*/
    {
        $this->to_type = $to_type;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "to_specific_users":
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
            case "to_specific_users":
                return json_decode($field_value, true);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
