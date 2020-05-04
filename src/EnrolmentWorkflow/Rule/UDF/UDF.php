<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field\Field;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\Operator;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value\Value;

/**
 * Class UDF
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UDF extends AbstractRule
{

    use Field;
    use Operator;
    use Value;

    const TABLE_NAME_SUFFIX = "udf";


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

        $descriptions[] = $this->field . " " . $this->getOperatorTitle() . "  " . $this->value;

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
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

        switch ($field_name) {
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

        switch ($field_name) {
            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
