<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilCheckboxInputGUI;
use ilDateTimeInputGUI;
use ilNumberInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AssistantsFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssistantsFormGUI extends ObjectPropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = AssistantsGUI::LANG_MODULE;


    /**
     * AssistantsFormGUI constructor
     *
     * @param AssistantsGUI $parent
     * @param Assistants    $object
     */
    public function __construct(AssistantsGUI $parent, Assistants $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return parent::getValue($key);
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
                    "user_id" => [
                        self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        "setTitle"              => $this->txt("user")
                    ],
                    "until"   => [
                        self::PROPERTY_CLASS => ilDateTimeInputGUI::class
                    ],
                    "active"  => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ]
                ],
                "setShowSort"           => false,
                self::PROPERTY_REQUIRED => true
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
        $this->setTitle($this->txt("assistants"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                parent::storeValue($key, $value);
                break;
        }
    }
}
