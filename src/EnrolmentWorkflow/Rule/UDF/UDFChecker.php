<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use DateTime;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorChecker;
use stdClass;

/**
 * Class UDFChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UDFChecker extends AbstractRuleChecker
{

    use OperatorChecker;

    /**
     * @var UDF
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(UDF $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        $time = time();

        foreach ($this->getUserIds($user_id) as $user_id) {
            $user = self::srUserEnrolment()->getIliasObjectById($user_id);

            $udf_values = $user->getUserDefinedData();

            $field_id = self::srUserEnrolment()->excelImport()->getUserDefinedFieldID($this->rule->getField());
            if (empty($field_id) || empty($udf_value = strval($udf_values[($field_id = "f_" . $field_id)]))) {
                return false;
            }

            switch ($this->rule->getValueType()) {
                case UDF::VALUE_TYPE_TEXT:
                    $value = $this->rule->getValue();
                    break;

                case UDF::VALUE_TYPE_DATE:
                    if (empty($udf_value = DateTime::createFromFormat(UDF::VALUE_TYPE_DATE_FORMAT, $udf_value))
                    ) {
                        return false;
                    }

                    $udf_value = $udf_value->getTimestamp();

                    $value = $time;
                    break;

                default:
                    return false;
            }

            if ($this->checkOperator($udf_value, $value, $this->rule->getOperator(),
                $this->rule->isOperatorNegated(), $this->rule->isOperatorCaseSensitive())
            ) {
                return true;
            }
        }

        return false;
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return array_map(function (int $user_id) : stdClass {
            return (object) [
                "obj_ref_id" => $this->rule->getParentId(),
                "user_id"    => $user_id
            ];
        }, array_keys(self::srUserEnrolment()->ruleEnrolment()->getUsers()));
    }


    /**
     * @param int $user_id
     *
     * @return int[]
     */
    protected function getUserIds(int $user_id) : array
    {
        return [$user_id];
    }
}
