<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator;

/**
 * Trait OperatorChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait OperatorChecker
{

    /**
     * @param mixed $value
     * @param mixed $check_value
     * @param int   $operator
     * @param bool  $operator_negated
     * @param bool  $operator_case_sensitive
     *
     * @return bool
     */
    public function checkOperator($value, $check_value, int $operator, bool $operator_negated, bool $operator_case_sensitive) : bool
    {
        $value = $this->checkOperatorCaseSensitive($value, $operator_case_sensitive);
        $check_value = $this->checkOperatorCaseSensitive($check_value, $operator_case_sensitive);

        switch ($operator) {
            case OperatorConstants::OPERATOR_EQUALS:
                $check = ($value == $check_value);
                break;

            case OperatorConstants::OPERATOR_STARTS_WITH:
                $check = (strpos($value, $check_value) === 0);
                break;

            case OperatorConstants::OPERATOR_CONTAINS:
                $check = (strpos($value, $check_value) !== false);
                break;

            case OperatorConstants::OPERATOR_ENDS_WITH:
                $check = (strrpos($value, $check_value) === (strlen($value) - strlen($check_value)));
                break;

            case OperatorConstants::OPERATOR_REG_EX:
                // Fix RegExp
                if ($check_value[0] !== "/" && $check_value[strlen($check_value) - 1] !== "/") {
                    $check_value = "/$check_value/";
                }
                $check = (preg_match($check_value, $value) === 1);
                break;

            case OperatorConstants::OPERATOR_LESS:
                $check = ($value < $check_value);
                break;

            case OperatorConstants::OPERATOR_LESS_EQUALS:
                $check = ($value <= $check_value);
                break;

            case OperatorConstants::OPERATOR_BIGGER:
                $check = ($value > $check_value);
                break;

            case OperatorConstants::OPERATOR_BIGGER_EQUALS:
                $check = ($value >= $check_value);
                break;

            default:
                return false;
        }

        $check = $this->checkOperatorNegated($check, $operator_negated);

        return $check;
    }


    /**
     * @param mixed $value
     * @param bool  $operator_case_sensitive
     *
     * @return mixed
     */
    public function checkOperatorCaseSensitive($value, bool $operator_case_sensitive)
    {
        if (is_string($value)) {
            if (!$operator_case_sensitive) {
                $value = strtolower($value);
            }
        }

        return $value;
    }


    /**
     * @param bool $check
     * @param bool $operator_negated
     *
     * @return bool
     */
    public function checkOperatorNegated(bool $check, bool $operator_negated) : bool
    {
        if ($operator_negated) {
            $check = (!$check);
        }

        return $check;
    }
}
