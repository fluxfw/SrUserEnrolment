<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\Deputy;

/**
 * Class NotEditedRequestsInMyResponsibilityRequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NotEditedRequestsInMyResponsibilityRequestsTableGUI extends AbstractRequestsTableGUI
{

    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "edited_status"      => [
                "id"      => "edited_status",
                "default" => true,
                "sort"    => false
            ],
            "created_time_group" => [
                "id"      => "created_time_group",
                "default" => true,
                "sort"    => false
            ],
            "created_user"       => [
                "id"      => "created_user",
                "default" => true,
                "sort"    => false
            ],
            "object_title"       => [
                "id"      => "object_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ],
            "object_start"       => [
                "id"      => "object_start",
                "default" => true,
                "sort"    => false
            ],
            "object_end"         => [
                "id"      => "object_end",
                "default" => true,
                "sort"    => false
            ],
            "user_firstname"     => [
                "id"      => "user_firstname",
                "default" => true,
                "sort"    => false
            ],
            "user_lastname"      => [
                "id"      => "user_lastname",
                "default" => true,
                "sort"    => false
            ],
            "user_email"         => [
                "id"      => "user_email",
                "default" => true,
                "sort"    => false
            ],
            "user_org_units"     => [
                "id"      => "user_org_units",
                "default" => true,
                "sort"    => false
            ]
        ];

        foreach ($this->modifications as $modification) {
            $columns = array_merge($columns, $modification->getAdditionalColumns($this->parent_obj->getRequestsType()));
        }

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterEdited()/* : ?bool*/
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterEditedStatus()/* : ?array*/
    {
        $filter = $this->getFilterValues();

        $edited_status = $filter["edited_status"];

        return $edited_status;
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
        $responsible_users = [self::dic()->user()->getId()];

        if (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId())) {

            $responsible_users = array_merge($responsible_users, array_map(function (Deputy $deputy) : int {
                return $deputy->getUserId();
            }, self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getDeputiesOf(self::dic()->user()->getId())));
        }

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
    protected function getFilterUserId()/* : ?array*/
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
        $filter = $this->getFilterValues();

        $user_org_units = $filter["user_org_units"];

        return $user_org_units;
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
            "edited_status"  => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (string $edited_status_lang_key) : string {
                    return $this->txt("edited_status_" . $edited_status_lang_key);
                }, RequestGroup::EDITED_STATUS)
            ],
            "object_title"   => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ],
            "user_firstname" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_lastname"  => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_org_units" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields($this->parent_obj->getRequestsType()));
        }
    }
}
