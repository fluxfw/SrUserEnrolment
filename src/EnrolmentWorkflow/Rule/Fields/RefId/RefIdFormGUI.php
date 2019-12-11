<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId;

use ilNumberInputGUI;

/**
 * Trait RefIdFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait RefIdFormGUI
{

    /**
     * @return array
     */
    protected function getRefIdFormFields() : array
    {
        return [
            "ref_id" => [
                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                self::PROPERTY_REQUIRED => true
            ]
        ];
    }
}