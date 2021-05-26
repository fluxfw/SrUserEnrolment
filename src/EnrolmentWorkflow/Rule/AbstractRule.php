<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use LogicException;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRule
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 */
abstract class AbstractRule extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const ENROLL_BY_USER
        = [
            AbstractRule::PARENT_CONTEXT_COURSE,
            AbstractRule::PARENT_CONTEXT_ROLE
        ];
    const PARENT_CONTEXT_ACTION = 3;
    const PARENT_CONTEXT_COURSE = 1;
    const PARENT_CONTEXT_ROLE = 5;
    const PARENT_CONTEXT_RULE_GROUP = 4;
    const PARENT_CONTEXT_STEP = 2;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var string
     *
     * @abstract
     */
    const TABLE_NAME_SUFFIX = "";
    const TYPES
        = [
            self::PARENT_CONTEXT_STEP       => [
                self::TYPE_STEP_ACTION       => "action",
                self::TYPE_STEP_CHECK_ACTION => "check_action"
            ],
            self::PARENT_CONTEXT_ACTION     => [
                self::TYPE_ACTION_IF => "if"
            ],
            self::PARENT_CONTEXT_COURSE     => [
                self::TYPE_COURSE_RULE => "course_rule"
            ],
            self::PARENT_CONTEXT_RULE_GROUP => [
                self::TYPE_RULE_GROUP => "group"
            ],
            self::PARENT_CONTEXT_ROLE       => [
                self::TYPE_ROLE_RULE => "role_rule"
            ]
        ];
    const TYPE_ACTION_IF = 4;
    const TYPE_COURSE_RULE = 1;
    const TYPE_ROLE_RULE = 6;
    const TYPE_RULE_GROUP = 5;
    const TYPE_STEP_ACTION = 2;
    const TYPE_STEP_CHECK_ACTION = 3;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $enabled = true;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $enroll_type = null;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $parent_context;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $parent_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $rule_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $sort = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $type;


    /**
     * AbstractRule constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return string
     */
    public static function getRuleType() : string
    {
        $parts = explode("\\", static::class);

        return strtolower(end($parts));
    }


    /**
     * @inheritDoc
     */
    public static function getTableName() : string
    {
        if (empty(static::TABLE_NAME_SUFFIX)) {
            throw new LogicException("table name suffix is empty!");
        }

        return ilSrUserEnrolmentPlugin::PLUGIN_ID . "_rul_" . static::TABLE_NAME_SUFFIX;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return static::getTableName();
    }


    /**
     * @param int|null $parent_context
     *
     * @return bool
     */
    public static abstract function supportsParentContext(/*?*/ int $parent_context = null) : bool;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return static::getTableName();
    }


    /**
     * @return int|null
     */
    public function getEnrollType()/* : ?int*/
    {
        if (empty($this->enroll_type)) {
            return ($this->getParentContext() === self::TYPE_COURSE_RULE ? Member::TYPE_MEMBER : null);
        }

        return intval($this->enroll_type);
    }


    /**
     * @param int|null $enroll_type
     */
    public function setEnrollType(/*?*/ int $enroll_type = null) /*: void*/
    {
        $this->enroll_type = $enroll_type;
    }


    /**
     * @return string
     */
    public function getId() : string
    {
        return self::getRuleType() . "_" . $this->rule_id;
    }


    /**
     * @return int
     */
    public function getParentContext() : int
    {
        return $this->parent_context;
    }


    /**
     * @param int $parent_context
     */
    public function setParentContext(int $parent_context)/* : void*/
    {
        $this->parent_context = $parent_context;
    }


    /**
     * @return string
     */
    public function getParentId() : string
    {
        return $this->parent_id;
    }


    /**
     * @param string $parent_id
     */
    public function setParentId(string $parent_id)/* : void*/
    {
        $this->parent_id = $parent_id;
    }


    /**
     * @return string
     */
    public abstract function getRuleDescription() : string;


    /**
     * @return int
     */
    public function getRuleId() : int
    {
        return $this->rule_id;
    }


    /**
     * @param int $rule_id
     */
    public function setRuleId(int $rule_id)/*: void*/
    {
        $this->rule_id = $rule_id;
    }


    /**
     * @return string
     */
    public function getRuleTitle() : string
    {
        return $this->getRuleTypeTitle();
    }


    /**
     * @return string
     */
    public function getRuleTypeTitle() : string
    {
        return self::plugin()->translate("rule_type_" . static::getRuleType(), RulesGUI::LANG_MODULE);
    }


    /**
     * @return int
     */
    public function getSort() : int
    {
        return $this->sort;
    }


    /**
     * @param int $sort
     */
    public function setSort(int $sort)/*: void*/
    {
        $this->sort = $sort;
    }


    /**
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }


    /**
     * @param int $type
     */
    public function setType(int $type) /*: void*/
    {
        $this->type = $type;
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }


    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)/*: void*/
    {
        $this->enabled = $enabled;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "enabled":
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
        switch ($field_name) {
            case "enabled":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
