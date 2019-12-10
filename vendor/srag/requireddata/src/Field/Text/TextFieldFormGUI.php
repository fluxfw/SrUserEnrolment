<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Text;

use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\AbstractFieldFormGUI;

/**
 * Class TextFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Text
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TextFieldFormGUI extends AbstractFieldFormGUI
{

    /**
     * @var TextField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function getFormFields() : array
    {
        return [
            PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
        ];
    }
}
