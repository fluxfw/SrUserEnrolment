<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilSelectInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;

/**
 * Class AllRequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AllRequestsTableGUI extends AbstractRequestsTableGUI
{

    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "accepted"          => [
                "id"      => "accepted",
                "default" => true,
                "sort"    => false
            ],
            "create_time"       => [
                "id"      => "create_time",
                "default" => true,
                "sort"    => false,
            ],
            "create_user"       => [
                "id"      => "create_user",
                "default" => true,
                "sort"    => false,
            ],
            "object_title"      => [
                "id"      => "object_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ],
            "object_start"      => [
                "id"      => "object_start",
                "default" => true,
                "sort"    => false,
            ],
            "object_end"        => [
                "id"      => "object_end",
                "default" => true,
                "sort"    => false,
            ],
            "user_firstname"    => [
                "id"      => "user_firstname",
                "default" => true,
                "sort"    => false
            ],
            "user_lastname"     => [
                "id"      => "user_lastname",
                "default" => true,
                "sort"    => false
            ],
            "user_email"        => [
                "id"      => "user_email",
                "default" => true,
                "sort"    => false
            ],
            "user_org_units"    => [
                "id"      => "user_org_units",
                "default" => true,
                "sort"    => false
            ],
            "responsible_users" => [
                "id"      => "responsible_users",
                "default" => true,
                "sort"    => false
            ]
        ];

        foreach ($this->modifications as $modification) {
            $columns = array_merge($columns, $modification->getAdditionalColumns());
        }

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterAccepted()/* : ?bool*/
    {
        $filter = $this->getFilterValues();

        $accepted = $filter["accepted"];

        if (!empty($accepted)) {
            $accepted = ($accepted === "yes");
        } else {
            $accepted = null;
        }

        return $accepted;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterObjRefId()/* : ?int*/
    {
        return $this->parent_obj->getObjRefId();
    }


    /**
     * @inheritDoc
     */
    protected function getFilterObjectTitle()/* : ?string*/
    {
        $filter = $this->getFilterValues();

        $object_title = $filter["object_title"];

        return $object_title;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterResponsibleUsers()/* : ?array*/
    {
        $filter = $this->getFilterValues();

        $responsible_users = $filter["responsible_users"];

        return $responsible_users;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterStepId()/* : ?int*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUsrId()/* : ?int*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserEmail()/* : ?string*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserFirstname()/* : ?string*/
    {
        $filter = $this->getFilterValues();

        $user_firstname = $filter["user_firstname"];

        return $user_firstname;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserLastname()/* : ?string*/
    {
        $filter = $this->getFilterValues();

        $user_lastname = $filter["user_lastname"];

        return $user_lastname;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserOrgUnits()/* : ?string*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterWorkflowId()/* : ?int*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [
            "accepted"          => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    ""    => "",
                    "no"  => $this->txt("no"),
                    "yes" => $this->txt("yes")
                ]
            ],
            "object_title"      => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ],
            "user_firstname"    => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_lastname"     => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "responsible_users" => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => self::srUserEnrolment()->ruleEnrolment()->searchUsers(),
                "setAjaxLink"                     => self::dic()->ctrl()->getLinkTarget($this->parent_obj, RequestsGUI::CMD_GET_USERS_AUTO_COMPLETE, "", true, false)
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields());
        }
    }
}
