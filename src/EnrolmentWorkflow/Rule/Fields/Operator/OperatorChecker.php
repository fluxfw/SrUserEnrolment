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
     *
     * @return bool
     */
    public function checkOperator($value, $check_value) : bool
    {
        $value = $this->checkOperatorCaseSensitive($value);
        $check_value = $this->checkOperatorCaseSensitive($check_value);

        switch ($this->rule->getOperator()) {
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

        $check = $this->checkOperatorNegated($check);

        return $check;
    }


    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function checkOperatorCaseSensitive($value)
    {
        if (is_string($value)) {
            if (!$this->rule->isOperatorCaseSensitive()) {
                $value = strtolower($value);
            }
        }

        return $value;
    }


    /**
     * @param bool $check
     *
     * @return bool
     */
    public function checkOperatorNegated(bool $check) : bool
    {
        if ($this->rule->isOperatorNegated()) {
            $check = (!$check);
        }

        return $check;
    }
}
