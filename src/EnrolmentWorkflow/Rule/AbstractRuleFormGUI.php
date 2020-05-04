<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilCheckboxInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI;
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

        if ($this->rule->getParentContext() === AbstractRule::TYPE_COURSE_RULE) {
            $this->fields["enroll_type"] = [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_SUBITEMS => array_map(function (string $type_lang_key) : array {
                    return [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::plugin()->translate("member_type_" . $type_lang_key, MembersGUI::LANG_MODULE)
                    ];
                }, Member::TYPES_CORE),
                "setTitle"              => self::plugin()->translate("enroll_users_as", MembersGUI::LANG_MODULE, [""])
            ];
        }
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
