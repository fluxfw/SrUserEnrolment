<?php

namespace srag\RequiredData\SrUserEnrolment\Field;

use ilCheckboxInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\TabsInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TextAreaInputGUI\TextAreaInputGUI;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class AbstractFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFieldFormGUI extends ObjectPropertyFormGUI
{

    use RequiredDataTrait;
    const LANG_MODULE = FieldsCtrl::LANG_MODULE;
    /**
     * @var AbstractField
     */
    protected $object;


    /**
     * AbstractFieldFormGUI constructor
     *
     * @param FieldCtrl     $parent
     * @param AbstractField $object
     */
    public function __construct(FieldCtrl $parent, AbstractField $object)
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
        $this->addCommandButton(FieldCtrl::CMD_UPDATE_FIELD, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        $this->fields = [
            "enabled"      => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "required"     => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "labels"       => [
                self::PROPERTY_CLASS    => TabsInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                    "label" => [
                        self::PROPERTY_CLASS => ilTextInputGUI::class
                    ]
                ], true),
                "setTitle"              => $this->txt("label")
            ],
            "descriptions" => [
                self::PROPERTY_CLASS    => TabsInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                    "description" => [
                        self::PROPERTY_CLASS => TextAreaInputGUI::class,
                        "setRows"            => 10
                    ]
                ], true, false),
                "setTitle"              => $this->txt("description")
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
        $this->setTitle($this->txt("edit_field"));
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


    /**
     * @inheritDoc
     */
    public function txt(/*string*/ $key,/*?string*/ $default = null) : string
    {
        if ($default !== null) {
            return self::requiredData()->getPlugin()->translate($key, self::LANG_MODULE, [], true, "", $default);
        } else {
            return self::requiredData()->getPlugin()->translate($key, self::LANG_MODULE);
        }
    }
}
