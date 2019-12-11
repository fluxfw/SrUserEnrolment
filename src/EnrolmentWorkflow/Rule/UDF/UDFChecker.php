<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF;

use ilObjUser;
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
        $user = new ilObjUser($user_id);

        $udf_values = $user->getUserDefinedData();

        $field_id = self::srUserEnrolment()->excelImport()->getUserDefinedFieldID($this->rule->getField());
        if (empty($field_id) || empty($udf_value = strval($udf_values[($field_id = "f_" . $field_id)]))) {
            return false;
        }

        return $this->checkOperator($udf_value, $this->rule->getValue());
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
}