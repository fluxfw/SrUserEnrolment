<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRuleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractRuleFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = RulesGUI::LANG_MODULE;
    /**
     * @var AbstractRule
     */
    protected $rule;


    /**
     * AbstractRuleFormGUI constructor
     *
     * @param RuleGUI      $parent
     * @param AbstractRule $rule
     */
    public function __construct(RuleGUI $parent, AbstractRule $rule)
    {
        $this->rule = $rule;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return Items::getter($this->rule, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(RuleGUI::CMD_UPDATE_RULE, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "enabled" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("edit_rule"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->rule, $key, $value);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        self::srUserEnrolment()->enrolmentWorkflow()->rules()->storeRule($this->rule);

        return true;
    }
}
