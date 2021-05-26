<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values;

/**
 * Trait Values
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values
 */
trait Values
{

    /**
     * @var string[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $values = [];


    /**
     * @return string[]
     */
    public function getValues() : array
    {
        return $this->values;
    }


    /**
     * @param string[] $values
     */
    public function setValues(array $values)/* : void*/
    {
        $this->values = $values;
    }


    /**
     * @param string $field_name
     * @param mixed  $field_value
     *
     * @return mixed
     */
    protected function sleepValues(string $field_name, $field_value)
    {
        switch ($field_name) {
            case "values":
                return json_encode($field_value);

            default:
                return null;
        }
    }


    /**
     * @param string $field_name
     * @param mixed  $field_value
     *
     * @return mixed
     */
    protected function wakeUpValues(string $field_name, $field_value)
    {
        switch ($field_name) {
            case "values":
                return json_decode($field_value, true);

            default:
                return null;
        }
    }
}
