<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Trait Operator
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator
 */
trait Operator
{

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       2
     * @con_is_notnull   true
     */
    protected $operator = OperatorConstants::OPERATOR_EQUALS;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $operator_case_sensitive = false;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $operator_negated = false;


    /**
     * @return int
     */
    public function getOperator() : int
    {
        return $this->operator;
    }


    /**
     * @param int $operator
     */
    public function setOperator(int $operator)/* : void*/
    {
        $this->operator = $operator;
    }


    /**
     * @return bool
     */
    public function isOperatorCaseSensitive() : bool
    {
        return $this->operator_case_sensitive;
    }


    /**
     * @param bool $operator_case_sensitive
     */
    public function setOperatorCaseSensitive(bool $operator_case_sensitive)/* : void*/
    {
        $this->operator_case_sensitive = $operator_case_sensitive;
    }


    /**
     * @return bool
     */
    public function isOperatorNegated() : bool
    {
        return $this->operator_negated;
    }


    /**
     * @param bool $operator_negated
     */
    public function setOperatorNegated(bool $operator_negated)/* : void*/
    {
        $this->operator_negated = $operator_negated;
    }


    /**
     * @return string
     */
    protected function getOperatorTitle() : string
    {
        return self::plugin()->translate("operator_" . (OperatorConstants::OPERATORS[$this->operator] ?? OperatorConstants::OPERATORS_SUBSEQUENT[$this->operator]), RulesGUI::LANG_MODULE);
    }


    /**
     * @param string $field_name
     * @param mixed  $field_value
     *
     * @return mixed
     */
    protected function sleepOperator(string $field_name, $field_value)
    {
        switch ($field_name) {
            case "operator_case_sensitive":
            case "operator_negated":
                return ($field_value ? 1 : 0);

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
    protected function wakeUpOperator(string $field_name, $field_value)
    {
        switch ($field_name) {
            case "operator_case_sensitive":
            case "operator_negated":
                return boolval($field_value);

            default:
                return null;
        }
    }
}
