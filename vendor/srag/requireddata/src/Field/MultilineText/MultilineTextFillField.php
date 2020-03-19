<?php

namespace srag\RequiredData\SrUserEnrolment\Field\MultilineText;

use ilTextAreaInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TextAreaInputGUI\TextAreaInputGUI;
use srag\RequiredData\SrUserEnrolment\Field\Text\TextFillField;

/**
 * Class MultilineTextFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\MultilineText
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultilineTextFillField extends TextFillField
{

    /**
     * @var MultilineTextField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(MultilineTextField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function getFormFields() : array
    {
        return [
            PropertyFormGUI::PROPERTY_CLASS => TextAreaInputGUI::class
        ];
    }


    /**
     * @inheritDoc
     */
    public function formatAsString($fill_value) : string
    {
        return nl2br(parent::formatAsString($fill_value), false);
    }
}
