<?php

namespace srag\RequiredData\SrUserEnrolment\Field\DynamicValue;

use ilNonEditableValueGUI;
use srag\CustomInputGUIs\SrUserEnrolment\HiddenInputGUI\HiddenInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\RequiredData\SrUserEnrolment\Fill\AbstractFillField;

/**
 * Class DynamicValueFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\DynamicValue
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class DynamicValueFillField extends AbstractFillField
{

    /**
     * @var DynamicValueField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(DynamicValueField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function getFormFields() : array
    {
        if ($this->field->isHide()) {
            return [
                PropertyFormGUI::PROPERTY_CLASS => HiddenInputGUI::class
            ];
        } else {
            return [
                PropertyFormGUI::PROPERTY_CLASS => ilNonEditableValueGUI::class,
                PropertyFormGUI::PROPERTY_VALUE => $this->field->deliverDynamicValue(),
            ];
        }
    }


    /**
     * @inheritDoc
     */
    public function formatAsJson($fill_value)
    {
        return $this->field->deliverDynamicValue();
    }


    /**
     * @inheritDoc
     */
    public function formatAsString($fill_value) : string
    {
        return htmlspecialchars($fill_value);
    }
}
