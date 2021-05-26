<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator;

/**
 * Trait OperatorChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator
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
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return ($value == $check_value);
                });
                break;

            case OperatorConstants::OPERATOR_STARTS_WITH:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return (strpos($value, $check_value) === 0);
                });
                break;

            case OperatorConstants::OPERATOR_CONTAINS:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return (strpos($value, $check_value) !== false);
                });
                break;

            case OperatorConstants::OPERATOR_ENDS_WITH:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return (strrpos($value, $check_value) === (strlen($value) - strlen($check_value)));
                });
                break;

            case OperatorConstants::OPERATOR_REG_EX:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    // Fix RegExp
                    if ($check_value[0] !== "/" && $check_value[strlen($check_value) - 1] !== "/") {
                        $check_value = "/$check_value/";
                    }

                    return (preg_match($check_value, $value) === 1);
                });
                break;

            case OperatorConstants::OPERATOR_LESS:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return ($value < $check_value);
                });
                break;

            case OperatorConstants::OPERATOR_LESS_EQUALS:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return ($value <= $check_value);
                });
                break;

            case OperatorConstants::OPERATOR_BIGGER:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return ($value > $check_value);
                });
                break;

            case OperatorConstants::OPERATOR_BIGGER_EQUALS:
                $check = $this->checkOperatorCheck($value, $check_value, function ($value, $check_value) : bool {
                    return ($value >= $check_value);
                });
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
        if (!$operator_case_sensitive) {
            if (is_array($value)) {
                $value = array_map("strtolower", $value);
            } else {
                if (is_string($value)) {
                    $value = strtolower($value);
                }
            }
        }

        return $value;
    }


    /**
     * @param mixed    $value
     * @param mixed    $check_value
     * @param callable $callback
     *
     * @return bool
     */
    public function checkOperatorCheck($value, $check_value, callable $callback) : bool
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        if (!is_array($check_value)) {
            $check_value = [$check_value];
        }

        foreach ($value as $value_) {
            foreach ($check_value as $check_value_) {
                if ($callback($value_, $check_value_)) {
                    return true;
                }
            }
        }

        return false;
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
