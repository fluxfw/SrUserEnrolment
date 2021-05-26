<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field;

use ilTextInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Trait FieldFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field
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
                self::PROPERTY_REQUIRED => true,
                "setTitle"              => self::plugin()->translate("field", RulesGUI::LANG_MODULE)
            ]
        ];
    }
}
