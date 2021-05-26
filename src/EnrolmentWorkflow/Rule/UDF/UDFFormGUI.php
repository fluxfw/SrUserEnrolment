<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field\FieldFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values\ValuesFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class UDFFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF
 */
class UDFFormGUI extends AbstractRuleFormGUI
{

    use FieldFormGUI;
    use OperatorFormGUI;
    use ValuesFormGUI;

    /**
     * @var UDF
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, UDF $rule)
    {
        parent::__construct($parent, $rule);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        $value_values = $this->getValueValues($key);
        if ($value_values !== null) {
            return $value_values;
        }

        switch ($key) {
            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            $this->getFieldFormFields(),
            $this->getOperatorFormFields1(),
            [
                "value_type" => [
                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => array_combine(array_keys(UDF::VALUE_TYPES), array_map(function (string $value_type_lang_key, string $value_type) : array {
                        $field = [
                            self::PROPERTY_CLASS => ilRadioOption::class,
                            "setTitle"           => $this->txt("value_type_" . $value_type_lang_key)
                        ];

                        switch ($value_type) {
                            case UDF::VALUE_TYPE_TEXT:
                                $field[self::PROPERTY_SUBITEMS] = $this->getValuesFormFields();
                                break;

                            case UDF::VALUE_TYPE_DATE:
                                $field["setInfo"] = self::plugin()->translate("value_type_" . $value_type_lang_key . "_info", self::LANG_MODULE, [UDF::VALUE_TYPE_DATE_FORMAT]);
                                break;

                            default:
                                break;
                        }

                        return $field;
                    }, UDF::VALUE_TYPES, array_keys(UDF::VALUE_TYPES)))
                ]
            ],
            $this->getOperatorFormFields2()
        );
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value)/*: void*/
    {
        $value_values = $this->storeValueValues($key, $value);
        if ($value_values) {
            return;
        }

        switch ($key) {
            default:
                parent::storeValue($key, $value);
                break;
        }
    }
}
