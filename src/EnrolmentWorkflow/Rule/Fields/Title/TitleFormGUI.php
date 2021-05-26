<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title;

use ilTextInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Trait TitleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title
 */
trait TitleFormGUI
{

    /**
     * @return array
     */
    protected function getTitleFormFields() : array
    {
        return [
            "title" => [
                self::PROPERTY_CLASS    => ilTextInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                "setTitle"              => self::plugin()->translate("title", RulesGUI::LANG_MODULE)
            ]
        ];
    }
}
