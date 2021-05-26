<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;

/**
 * Class OwnRequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 */
class OwnRequestsTableGUI extends AbstractRequestsTableGUI
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
            "responsible_users"  => [
                "id"      => "responsible_users",
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
        return null;
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
        return null;
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
    protected function getFilterUserEmail()/* : ?string*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserFirstname()/* : ?string*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserId()/* : ?array*/
    {
        return [self::dic()->user()->getId()];
    }


    /**
     * @inheritDoc
     */
    protected function getFilterUserLastname()/* : ?string*/
    {
        return null;
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
            "edited_status" => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (string $edited_status_lang_key) : string {
                    return $this->txt("edited_status_" . $edited_status_lang_key);
                }, RequestGroup::EDITED_STATUS)
            ],
            "object_title"  => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields($this->parent_obj->getRequestsType()));
        }
    }
}
