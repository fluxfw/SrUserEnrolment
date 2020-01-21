<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title;

use ilTextInputGUI;

/**
 * Trait TitleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
                "setTitle"              => $this->txt("title")
            ]
        ];
    }
}
