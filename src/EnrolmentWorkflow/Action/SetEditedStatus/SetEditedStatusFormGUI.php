<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus;

use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestGroup;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;

/**
 * Class SetEditedStatusFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus
 */
class SetEditedStatusFormGUI extends AbstractActionFormGUI
{

    /**
     * @var SetEditedStatus
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, SetEditedStatus $action)
    {
        parent::__construct($parent, $action);
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
                "edited_status" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => array_map(function (string $edited_status_lang_key) : string {
                        return self::plugin()->translate("edited_status_" . $edited_status_lang_key, RequestsGUI::LANG_MODULE);
                    }, RequestGroup::EDITED_STATUS),
                    "setTitle"              => self::plugin()->translate("edited_status", RequestsGUI::LANG_MODULE)
                ]
            ]
        );
    }
}
