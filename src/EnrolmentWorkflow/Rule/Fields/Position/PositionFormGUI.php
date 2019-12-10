<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position;

use ilSelectInputGUI;

/**
 * Trait PositionFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait PositionFormGUI
{

    /**
     * @param bool $required
     *
     * @return array
     */
    protected function getPositionFormFields(bool $required = true) : array
    {
        return [
            "position" => [
                self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                self::PROPERTY_REQUIRED => $required,
                self::PROPERTY_OPTIONS  => [POSITION_ALL => ""] + self::srUserEnrolment()->ruleEnrolment()->getPositions()
            ]
        ];
    }
}
