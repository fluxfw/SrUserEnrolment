<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRuleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractRuleFormGUI extends ObjectPropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = RulesGUI::LANG_MODULE;
    /**
     * @var AbstractRule
     */
    protected $object;


    /**
     * AbstractRuleFormGUI constructor
     *
     * @param RuleGUI      $parent
     * @param AbstractRule $object
     */
    public function __construct(RuleGUI $parent, AbstractRule $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return parent::getValue($key);
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
                parent::storeValue($key, $value);
                break;
        }
    }
}
