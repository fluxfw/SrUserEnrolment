<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Select;

use ilSelectInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;
use srag\RequiredData\SrUserEnrolment\Fill\AbstractFillField;

/**
 * Class SelectFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Select
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SelectFillField extends AbstractFillField
{

    /**
     * @var SelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(SelectField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function getFormFields() : array
    {
        return [
            PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
            PropertyFormGUI::PROPERTY_OPTIONS => ["&lt;" . self::requiredData()->getPlugin()->translate("please_select", FieldsCtrl::LANG_MODULE) . "&gt;"] + $this->field->getSelectOptions()
        ];
    }


    /**
     * @inheritDoc
     */
    public function formatAsJson($fill_value)
    {
        return strval($fill_value);
    }


    /**
     * @inheritDoc
     */
    public function formatAsString($fill_value) : string
    {
        return htmlspecialchars($this->field->getSelectOptions()[strval($fill_value)]);
    }
}
