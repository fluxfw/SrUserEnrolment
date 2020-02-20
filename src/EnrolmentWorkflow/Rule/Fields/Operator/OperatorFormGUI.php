<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator;

use ilCheckboxInputGUI;
use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Trait OperatorFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait OperatorFormGUI
{

    /**
     * @return array
     */
    protected function getOperatorFormFields1() : array
    {
        return [
            "operator" => [
                self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_OPTIONS  => array_map(function (string $operator_lang_key) : string {
                    return self::plugin()->translate("operator_" . $operator_lang_key, RulesGUI::LANG_MODULE);
                }, OperatorConstants::OPERATORS),
                "setTitle"              => self::plugin()->translate("operator", RulesGUI::LANG_MODULE)
            ]
        ];
    }


    /**
     * @return array
     */
    protected function getOperatorFormFieldsSubsequent1() : array
    {
        return [
            "operator_subsequent" => [
                self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_OPTIONS  => array_map(function (string $operator_lang_key) : string {
                    return $this->txt("operator_" . $operator_lang_key);
                }, OperatorConstants::OPERATORS_SUBSEQUENT),
                "setTitle"              => $this->txt("operator")
            ]
        ];
    }


    /**
     * @return array
     */
    protected static function getOperatorFormFields2() : array
    {
        return [
            "operator_negated"        => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("operator_negated", RulesGUI::LANG_MODULE)
            ],
            "operator_case_sensitive" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("operator_case_sensitive", RulesGUI::LANG_MODULE)
            ]
        ];
    }
}
