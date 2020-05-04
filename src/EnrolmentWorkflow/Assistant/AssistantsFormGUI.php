<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilCheckboxInputGUI;
use ilDateTimeInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AssistantsFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssistantsFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = AssistantsGUI::LANG_MODULE;
    /**
     * @var array
     */
    protected $assistants;


    /**
     * AssistantsFormGUI constructor
     *
     * @param AssistantsGUI $parent
     * @param array         $assistants
     */
    public function __construct(AssistantsGUI $parent, array $assistants)
    {
        $this->assistants = $assistants;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return $this->{$key};
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(AssistantsGUI::CMD_UPDATE_ASSISTANTS, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "assistants" => [
                self::PROPERTY_CLASS    => MultiLineNewInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    "assistant_user_id" => [
                        self::PROPERTY_CLASS      => MultiSelectSearchNewInputGUI::class,
                        self::PROPERTY_REQUIRED   => true,
                        "setTitle"                => $this->txt("user"),
                        "setAjaxAutoCompleteCtrl" => new UsersAjaxAutoCompleteCtrl(),
                        "setLimitCount"           => 1
                    ],
                    "until"             => [
                        self::PROPERTY_CLASS => ilDateTimeInputGUI::class
                    ],
                    "active"            => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ]
                ],
                "setShowSort"           => false
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt(($this->parent->getUserId() === intval(self::dic()->user()->getId()) ? "my_" : "") . "assistants"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                $this->{$key} = $value;
                break;
        }
    }


    /**
     * @return array
     */
    public function getAssistants() : array
    {
        return $this->assistants;
    }
}
