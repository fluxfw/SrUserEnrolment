<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value;

/**
 * Trait Value
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait Value
{

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $value = "";


    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }


    /**
     * @param string $value
     */
    public function setValue(string $value)/* : void*/
    {
        $this->value = $value;
    }
}
