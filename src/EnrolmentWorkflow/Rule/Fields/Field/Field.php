<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field;

/**
 * Trait Field
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field
 */
trait Field
{

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $field = "";


    /**
     * @return string
     */
    public function getField() : string
    {
        return $this->field;
    }


    /**
     * @param string $field
     */
    public function setField(string $field)/* : void*/
    {
        $this->field = $field;
    }
}
