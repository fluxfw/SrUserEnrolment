<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\Group;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRulesGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RulesTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = RulesGUI::LANG_MODULE;


    /**
     * RulesTableGUI constructor
     *
     * @param RulesGUI $parent
     * @param string   $parent_cmd
     */
    public function __construct(RulesGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param AbstractRule $rule
     */
    protected function getColumnValue(/*string*/ $column, /*AbstractRule*/ $rule, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            case "enabled":
                if ($rule->isEnabled()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            case "rule_description":
                $column = $rule->getRuleDescription();
                break;

            case "enroll_type":
                $column = self::plugin()->translate("member_type_" . Member::TYPES[$rule->getEnrollType()], MembersGUI::LANG_MODULE);
                break;

            default:
                $column = htmlspecialchars(Items::getter($rule, $column));
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
                "enabled" => [
                    "id"      => "enabled",
                    "default" => true,
                    "sort"    => false
                ]
            ]
            + ($this->parent_obj->getParentContext() === AbstractRule::TYPE_COURSE_RULE ? [
                "enroll_type" => [
                    "id"      => "enroll_type",
                    "default" => true,
                    "sort"    => false,
                    "txt"     => self::plugin()->translate("enroll_users_as", MembersGUI::LANG_MODULE, [""])
                ]
            ] : [])
            + [
                "rule_type_title"  => [
                    "id"      => "rule_type_title",
                    "default" => true,
                    "sort"    => false,
                    "txt"     => $this->txt("rule_type")
                ],
                "rule_description" => [
                    "id"      => "rule_description",
                    "default" => true,
                    "sort"    => false
                ]
            ];

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/*: void*/
    {
        $this->addColumn("");

        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_rule"), self::dic()->ctrl()
            ->getLinkTargetByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::CMD_ADD_RULE)));

        $this->setSelectAllCheckbox(RuleGUI::GET_PARAM_RULE_ID . $this->parent_obj->getParentContext());
        $this->addMultiCommand(RulesGUI::CMD_ENABLE_RULES, $this->txt("enable_rules"));
        $this->addMultiCommand(RulesGUI::CMD_DISABLE_RULES, $this->txt("disable_rules"));
        $this->addMultiCommand(RulesGUI::CMD_REMOVE_RULES_CONFIRM, $this->txt("remove_rules"));
        if (!($this->parent_obj instanceof GroupRulesGUI)) {
            $this->addMultiCommand(RulesGUI::CMD_CREATE_GROUP_OF_RULES, $this->txt("create_group_of_rules"));
        }
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setData(self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRules($this->parent_obj->getParentContext(), $this->parent_obj->getType(), $this->parent_obj->getParentId(), false));
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_rules");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("type_" . AbstractRule::TYPES[$this->parent_obj->getParentContext()][$this->parent_obj->getType()]));
    }


    /**
     * @param AbstractRule $rule
     */
    protected function fillRow(/*AbstractRule*/ $rule)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::GET_PARAM_RULE_TYPE . $this->parent_obj->getParentContext(), $rule->getRuleType());
        self::dic()->ctrl()->setParameterByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::GET_PARAM_RULE_ID . $this->parent_obj->getParentContext(), $rule->getRuleId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariableEscaped("CHECKBOX_POST_VAR", RuleGUI::GET_PARAM_RULE_ID . $this->parent_obj->getParentContext());
        $this->tpl->setVariableEscaped("ID", $rule->getId());
        $this->tpl->parseCurrentBlock();

        parent::fillRow($rule);

        $actions = [
            self::dic()->ui()->factory()->link()->standard($this->txt("edit_rule"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::CMD_EDIT_RULE)),
            self::dic()->ui()->factory()->link()->standard($this->txt("remove_rule"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::CMD_REMOVE_RULE_CONFIRM))
        ];

        if (!($this->parent_obj instanceof GroupRulesGUI) && $rule instanceof Group) {
            $actions[] = self::dic()->ui()->factory()->link()->standard($this->txt("ungroup"), self::dic()->ctrl()
                ->getLinkTargetByClass($this->parent_obj->getRuleGUIClass(), RuleGUI::CMD_UNGROUP));
        }

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel($this->txt("actions"))));
    }
}
