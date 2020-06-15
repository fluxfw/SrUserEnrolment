<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class CreateRuleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateRuleFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = RulesGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var AbstractRule|null
     */
    protected $rule = null;
    /**
     * @var string
     */
    protected $rule_type;


    /**
     * CreateRuleFormGUI constructor
     *
     * @param RuleGUI $parent
     */
    public function __construct(RuleGUI $parent)
    {
        $this->rule_type = current(array_keys(self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->getRuleTypes($parent->getParent()->getParentContext())));

        parent::__construct($parent);
    }


    /**
     * @return AbstractRule
     */
    public function getRule() : AbstractRule
    {
        return $this->rule;
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        $this->rule = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newInstance($this->rule_type);

        $this->rule->setType($this->parent->getParent()->getType());
        $this->rule->setParentContext($this->parent->getParent()->getParentContext());
        $this->rule->setParentId($this->parent->getParent()->getParentId());

        self::srUserEnrolment()->enrolmentWorkflow()->rules()->storeRule($this->rule);

        return true;
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return $this->{$key};
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(RuleGUI::CMD_CREATE_RULE, $this->txt("add"));
        $this->addCommandButton(RuleGUI::CMD_BACK, $this->txt("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "rule_type" => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => array_map(function (string $class) : array {
                    return [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newInstance($class::getRuleType())->getRuleTypeTitle()
                    ];
                }, self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->getRuleTypes($this->parent->getParent()->getParentContext()))
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
        $this->setTitle($this->txt("add_rule"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                $this->{$key} = $value;
                break;
        }
    }
}
