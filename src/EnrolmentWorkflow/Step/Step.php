<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\MultilangualTabsInputGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Step
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 */
class Step extends ActiveRecord
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const REQUIRED_DATA_PARENT_CONTEXT_STEP = 1;
    const TABLE_NAME = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_stp";
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $action_accept_title = [];
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $action_title = [];
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
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $step_id;
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $title = [];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $workflow_id;


    /**
     * Step constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
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
     * @param string|null $lang_key
     * @param bool        $use_default_if_not_set
     *
     * @return string
     */
    public function getActionEditTitle(/*?*/ string $lang_key = null, bool $use_default_if_not_set = true) : string
    {
        return strval(MultilangualTabsInputGUI::getValueForLang($this->action_accept_title, $lang_key, "action_accept_title", $use_default_if_not_set));
    }


    /**
     * @return array
     */
    public function getActionEditTitles() : array
    {
        return $this->action_accept_title;
    }


    /**
     * @param string|null $lang_key
     * @param bool        $use_default_if_not_set
     *
     * @return string
     */
    public function getActionTitle(/*?*/ string $lang_key = null, bool $use_default_if_not_set = true) : string
    {
        return strval(MultilangualTabsInputGUI::getValueForLang($this->action_title, $lang_key, "action_title", $use_default_if_not_set));
    }


    /**
     * @param string $action_title
     * @param string $lang_key
     */
    public function setActionTitle(string $action_title, string $lang_key) : void
    {
        MultilangualTabsInputGUI::setValueForLang($this->action_title, $action_title, $lang_key, "action_title");
    }


    /**
     * @return array
     */
    public function getActionTitles() : array
    {
        return $this->action_title;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
    public function setSort(int $sort) : void
    {
        $this->sort = $sort;
    }


    /**
     * @return int
     */
    public function getStepId() : int
    {
        return $this->step_id;
    }


    /**
     * @param int $step_id
     */
    public function setStepId(int $step_id) : void
    {
        $this->step_id = $step_id;
    }


    /**
     * @param string|null $lang_key
     * @param bool        $use_default_if_not_set
     *
     * @return string
     */
    public function getTitle(/*?*/ string $lang_key = null, bool $use_default_if_not_set = true) : string
    {
        return strval(MultilangualTabsInputGUI::getValueForLang($this->title, $lang_key, "title", $use_default_if_not_set));
    }


    /**
     * @param string $title
     * @param string $lang_key
     */
    public function setTitle(string $title, string $lang_key) : void
    {
        MultilangualTabsInputGUI::setValueForLang($this->title, $title, $lang_key, "title");
    }


    /**
     * @return array
     */
    public function getTitles() : array
    {
        return $this->title;
    }


    /**
     * @return int
     */
    public function getWorkflowId() : int
    {
        return $this->workflow_id;
    }


    /**
     * @param int $workflow_id
     */
    public function setWorkflowId(int $workflow_id) : void
    {
        $this->workflow_id = $workflow_id;
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
    public function setEnabled(bool $enabled) : void
    {
        $this->enabled = $enabled;
    }


    /**
     * @param string $action_edit_title
     * @param string $lang_key
     */
    public function setActionEditTitle(string $action_edit_title, string $lang_key) : void
    {
        MultilangualTabsInputGUI::setValueForLang($this->action_accept_title, $action_edit_title, $lang_key, "action_accept_title");
    }


    /**
     * @param array $action_edit_titles
     */
    public function setActionEditTitles(array $action_edit_titles) : void
    {
        $this->action_accept_title = $action_edit_titles;
    }


    /**
     * @param array $action_titles
     */
    public function setActionTitles(array $action_titles) : void
    {
        $this->action_title = $action_titles;
    }


    /**
     * @param array $titles
     */
    public function setTitles(array $titles) : void
    {
        $this->title = $titles;
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

            case "action_accept_title":
            case "action_title":
            case "title":
                return json_encode($field_value);

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

            case "action_accept_title":
            case "action_title":
            case "title":
                return json_decode($field_value, true);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
