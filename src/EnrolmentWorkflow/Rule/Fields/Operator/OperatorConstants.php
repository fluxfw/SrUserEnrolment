<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator;

/**
 * Class OperatorConstants
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator
 */
final class OperatorConstants
{

    const OPERATORS
        = [
            self::OPERATOR_EQUALS        => "equals",
            self::OPERATOR_STARTS_WITH   => "starts_with",
            self::OPERATOR_CONTAINS      => "contains",
            self::OPERATOR_ENDS_WITH     => "ends_with",
            self::OPERATOR_REG_EX        => "reg_ex",
            self::OPERATOR_LESS          => "less",
            self::OPERATOR_LESS_EQUALS   => "less_equals",
            self::OPERATOR_BIGGER        => "bigger",
            self::OPERATOR_BIGGER_EQUALS => "bigger_equals"
        ];
    const OPERATORS_SUBSEQUENT
        = [
            self::OPERATOR_EQUALS            => "equals",
            self::OPERATOR_EQUALS_SUBSEQUENT => "equals_subsequent"
        ];
    const OPERATOR_BIGGER = 9;
    const OPERATOR_BIGGER_EQUALS = 10;
    const OPERATOR_CONTAINS = 3;
    const OPERATOR_ENDS_WITH = 4;
    const OPERATOR_EQUALS = 1;
    const OPERATOR_EQUALS_SUBSEQUENT = 6;
    const OPERATOR_LESS = 7;
    const OPERATOR_LESS_EQUALS = 8;
    const OPERATOR_REG_EX = 5;
    const OPERATOR_STARTS_WITH = 2;


    /**
     * OperatorConstants constructor
     */
    private function __construct()
    {

    }
}
