<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values;

use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value\ValueFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;

/**
 * Trait ValuesFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait ValuesFormGUI
{

    use ValueFormGUI;

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getValueValues(string $key)
    {
        switch ($key) {
            case "values":
                return array_map(function (string $value) : array {
                    return [
                        "value" => $value
                    ];
                }, parent::getValue($key));

            default:
                return null;
        }
    }


    /**
     * @return array
     */
    protected function getValuesFormFields() : array
    {
        return [
            "values" => [
                self::PROPERTY_CLASS    => MultiLineNewInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => $this->getValueFormFields(),
                "setTitle"              => self::plugin()->translate("value", RulesGUI::LANG_MODULE),
                "setShowInputLabel"     => MultiLineNewInputGUI::SHOW_INPUT_LABEL_NONE,
                "setShowSort"           => false
            ]
        ];
    }


    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    protected function storeValueValues(string $key, $value) : bool
    {
        switch ($key) {
            case "values":
                parent::storeValue($key, array_map(function (array $value) : string {
                    return $value["value"];
                }, $value));

                return true;

            default:
                return false;
        }
    }
}
