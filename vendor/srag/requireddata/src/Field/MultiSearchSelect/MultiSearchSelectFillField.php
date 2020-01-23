<?php

namespace srag\RequiredData\SrUserEnrolment\Field\MultiSearchSelect;

use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\MultiSelect\MultiSelectFillField;

/**
 * Class MultiSearchSelectFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\MultiSearchSelect
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiSearchSelectFillField extends MultiSelectFillField
{

    /**
     * @var MultiSearchSelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(MultiSearchSelectField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function getFormFields() : array
    {
        return [
            PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
            PropertyFormGUI::PROPERTY_OPTIONS => $this->field->getSelectOptions()
        ];
    }
}
