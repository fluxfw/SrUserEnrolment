<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field;

use ilTextInputGUI;

/**
 * Trait FieldFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait FieldFormGUI
{

    /**
     * @return array
     */
    protected function getFieldFormFields() : array
    {
        return [
            "field" => [
                self::PROPERTY_CLASS    => ilTextInputGUI::class,
                self::PROPERTY_REQUIRED => true
            ]
        ];
    }
}
