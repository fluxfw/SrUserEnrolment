<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field\Field;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\Operator;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Values\Values;

/**
 * Class UDF
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF
 */
class UDF extends AbstractRule
{

    use Field;
    use Operator;
    use Values;

    const TABLE_NAME_SUFFIX = "udf";
    const VALUE_TYPES
        = [
            self::VALUE_TYPE_TEXT => "text",
            self::VALUE_TYPE_DATE => "date"
        ];
    const VALUE_TYPE_DATE = 2;
    const VALUE_TYPE_DATE_FORMAT = "Y-m-d";
    const VALUE_TYPE_TEXT = 1;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $process_empty_values = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $value_type = self::VALUE_TYPE_TEXT;


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        $descriptions = [];

        $descriptions[] = $this->field . " " . $this->getOperatorTitle() . "  " . implode(", ", $this->values);

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
    }


    /**
     * @return int
     */
    public function getValueType() : int
    {
        if (empty($this->value_type)) {
            return self::VALUE_TYPE_TEXT;
        }

        return $this->value_type;
    }


    /**
     * @param int $value_type
     */
    public function setValueType(int $value_type) : void
    {
        $this->value_type = $value_type;
    }


    /**
     * @return bool
     */
    public function isProcessEmptyValues() : bool
    {
        return $this->process_empty_values;
    }


    /**
     * @param bool $process_empty_values
     */
    public function setProcessEmptyValues(bool $process_empty_values) : void
    {
        $this->process_empty_values = $process_empty_values;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        $field_value_operator = $this->sleepOperator($field_name, $field_value);
        if ($field_value_operator !== null) {
            return $field_value_operator;
        }

        $field_value_values = $this->sleepValues($field_name, $field_value);
        if ($field_value_values !== null) {
            return $field_value_values;
        }

        switch ($field_name) {
            case "process_empty_values":
                return ($field_value ? 1 : 0);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        $field_value_operator = $this->wakeUpOperator($field_name, $field_value);
        if ($field_value_operator !== null) {
            return $field_value_operator;
        }

        $field_value_values = $this->wakeUpValues($field_name, $field_value);
        if ($field_value_values !== null) {
            return $field_value_values;
        }

        switch ($field_name) {
            case "process_empty_values":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
