<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Rule
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated
 */
class Rule extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    /**
     * @var string
     *
     * @deprecated
     */
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_rule";
    /**
     * @var string
     *
     * @deprecated
     */
    const TABLE_NAME_ENROLLED = "srusrenr_enrolled";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var string
     *
     * @deprecated
     */
    const ORG_UNIT_TYPE_TITLE = 1;
    /**
     * @var string
     *
     * @deprecated
     */
    const ORG_UNIT_TYPE_TREE = 2;


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     *
     * @deprecated
     */
    protected $rule_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $object_id;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $enabled = true;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $org_unit_type = 1;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $title = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       2
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $operator = 0;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $operator_negated = false;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $operator_case_sensitive = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $ref_id = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     *
     * @deprecated
     */
    protected $position = 0;


    /**
     * Rule constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     *
     * @deprecated
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "object_id":
            case "operator":
            case "org_unit_type":
            case "position":
            case "ref_id":
            case "rule_id":
                return intval($field_value);

            case "enabled":
            case "operator_negated":
            case "operator_case_sensitive":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getRuleId() : int
    {
        return $this->rule_id;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getObjectId() : int
    {
        return $this->object_id;
    }


    /**
     * @return bool
     *
     * @deprecated
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getOrgUnitType() : int
    {
        return $this->org_unit_type;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function getTitle() : string
    {
        return $this->title;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getOperator() : int
    {
        return $this->operator;
    }


    /**
     * @return bool
     *
     * @deprecated
     */
    public function isOperatorNegated() : bool
    {
        return $this->operator_negated;
    }


    /**
     * @return bool
     *
     * @deprecated
     */
    public function isOperatorCaseSensitive() : bool
    {
        return $this->operator_case_sensitive;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getRefId() : int
    {
        return $this->ref_id;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getPosition() : int
    {
        return $this->position;
    }
}
