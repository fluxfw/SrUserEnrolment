<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers;

use ilMultiSelectInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchInputGUI\MultiSelectSearchInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;

/**
 * Class AssignResponsibleUsersFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssignResponsibleUsersFormGUI extends AbstractActionFormGUI
{

    /**
     * @var AssignResponsibleUsers
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, AssignResponsibleUsers $object)
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
                "users_type" => [
                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => array_combine(array_keys(AssignResponsibleUsers::USER_TYPES), array_map(function (string $user_type_lang_key, int $user_type) : array {
                        switch ($user_type) {
                            case AssignResponsibleUsers::USER_TYPE_POSITION:
                                $items = [
                                    "assign_positions" => [
                                        self::PROPERTY_CLASS    => ilMultiSelectInputGUI::class,
                                        self::PROPERTY_REQUIRED => true,
                                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getPositions()
                                    ]
                                ];
                                break;

                            case AssignResponsibleUsers::USER_TYPE_SPECIFIC_USERS:
                                $items = [
                                    "specific_users" => [
                                        self::PROPERTY_CLASS    => MultiSelectSearchInputGUI::class,
                                        self::PROPERTY_REQUIRED => true,
                                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->searchUsers(),
                                        "setAjaxLink"           => self::dic()->ctrl()->getLinkTarget($this->parent, ActionGUI::CMD_GET_USERS_AUTO_COMPLETE, "", true, false),
                                        "setTitle"              => $this->txt("userstype_specific_users")
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
                            "setTitle"              => $this->txt("userstype_" . $user_type_lang_key)
                        ];
                    }, AssignResponsibleUsers::USER_TYPES, array_keys(AssignResponsibleUsers::USER_TYPES))),
                    "setTitle"              => $this->txt("userstype")
                ]
            ]);
    }
}
