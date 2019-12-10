<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification;

use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchInputGUI\MultiSelectSearchInputGUI;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationInterface;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;

/**
 * Class SendNotificationFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SendNotificationFormGUI extends AbstractActionFormGUI
{

    /**
     * @var SendNotification
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, SendNotification $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            [
                "notification_name" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_combine(array_map(function (NotificationInterface $notification) : string {
                            return $notification->getName();
                        }, self::srUserEnrolment()->notifications4plugin()->notifications()
                            ->getNotifications()), array_map(function (NotificationInterface $notification) : string {
                            return $notification->getTitle();
                        }, self::srUserEnrolment()->notifications4plugin()->notifications()
                            ->getNotifications())),
                    "setTitle"              => self::plugin()->translate("template_selection", NotificationsCtrl::LANG_MODULE)
                ],
                "to_type"           => [
                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => array_combine(array_keys(SendNotification::TO_TYPES), array_map(function (string $to_type_lang_key, int $user_type) : array {
                        switch ($user_type) {
                            case SendNotification::TO_TYPE_SPECIFIC_USERS:
                                $items = [
                                    "to_specific_users" => [
                                        self::PROPERTY_CLASS    => MultiSelectSearchInputGUI::class,
                                        self::PROPERTY_REQUIRED => true,
                                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->searchUsers(),
                                        "setAjaxLink"           => self::dic()->ctrl()->getLinkTarget($this->parent, ActionGUI::CMD_GET_USERS_AUTO_COMPLETE, "", true, false),
                                        "setTitle"              => $this->txt("totype_specific_users")
                                    ]
                                ];
                                break;

                            default:
                                $items = [];
                                break;
                        }

                        return [
                            self::PROPERTY_CLASS    => ilRadioOption::class,
                            self::PROPERTY_SUBITEMS => $items,
                            "setTitle"              => $this->txt("totype_" . $to_type_lang_key)
                        ];
                    }, SendNotification::TO_TYPES, array_keys(SendNotification::TO_TYPES))),
                    "setTitle"              => $this->txt("totype")
                ]
            ]
        );
    }
}
