<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilSelectInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;

/**
 * Class OwnRequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OwnRequestsTableGUI extends AbstractRequestsTableGUI
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
            "create_time_workflow"       => [
                "id"      => "create_time_workflow",
                "default" => true,
                "sort"    => false
            ],
            "create_user"       => [
                "id"      => "create_user",
                "default" => true,
                "sort"    => false
            ],
            "object_title"      => [
                "id"      => "object_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
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
    protected function getFilterUsrId()/* : ?int*/
    {
        return self::dic()->user()->getId();
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
            "accepted"     => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    ""    => "",
                    "no"  => $this->txt("no"),
                    "yes" => $this->txt("yes")
                ]
            ],
            "object_title" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => self::plugin()->translate("object", RequestsGUI::LANG_MODULE)
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields());
        }
    }
}
