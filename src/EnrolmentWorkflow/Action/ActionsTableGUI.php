<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\CustomInputGUIs\SrUserEnrolment\Waiter\Waiter;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ActionsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ActionsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = ActionsGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * ActionsTableGUI constructor
     *
     * @param ActionsGUI $parent
     * @param string     $parent_cmd
     */
    public function __construct(ActionsGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "enabled"            => [
                "id"      => "enabled",
                "default" => true,
                "sort"    => false
            ],
            "type_title"         => [
                "id"      => "type_title",
                "default" => true,
                "sort"    => false,
                "txt"     => $this->txt("type")
            ],
            "action_description" => [
                "id"      => "action_description",
                "default" => true,
                "sort"    => false
            ],
            "if_description"     => [
                "id"      => "if_description",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("type_if", RulesGUI::LANG_MODULE)
            ]
        ];

        return $columns;
    }


    /**
     * @param AbstractAction $action
     */
    protected function fillRow(/*AbstractAction*/ $action)/*: void*/
    {
        if (self::version()->is6()) {
            $glyph_factory = self::dic()->ui()->factory()->symbol()->glyph();
        } else {
            $glyph_factory = self::dic()->ui()->factory()->glyph();
        }

        self::dic()->ctrl()->setParameterByClass(ActionGUI::class, ActionGUI::GET_PARAM_ACTION_TYPE, $action->getType());
        self::dic()->ctrl()->setParameterByClass(ActionGUI::class, ActionGUI::GET_PARAM_ACTION_ID, $action->getActionId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariableEscaped("CHECKBOX_POST_VAR", ActionGUI::GET_PARAM_ACTION_ID);
        $this->tpl->setVariableEscaped("ID", $action->getId());
        $this->tpl->parseCurrentBlock();
        $this->tpl->setCurrentBlock("column");
        $this->tpl->setVariable("COLUMN", self::output()->getHTML([
            $glyph_factory->sortAscending()->withAdditionalOnLoadCode(function (string $id) use ($glyph_factory): string {
                Waiter::init(Waiter::TYPE_WAITER);

                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                    url: ' . json_encode(self::dic()
                        ->ctrl()
                        ->getLinkTargetByClass(ActionGUI::class, ActionGUI::CMD_MOVE_ACTION_UP, "", true)) . ',
                    type: "GET"
                 }).always(function () {
                    il.waiter.hide();
               }).success(function() {
                    row.insertBefore(row.prev());
                });
            });';
            }),
            $glyph_factory->sortDescending()->withAdditionalOnLoadCode(function (string $id) use ($glyph_factory): string {
                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                    url: ' . json_encode(self::dic()
                        ->ctrl()
                        ->getLinkTargetByClass(ActionGUI::class, ActionGUI::CMD_MOVE_ACTION_DOWN, "", true)) . ',
                    type: "GET"
                }).always(function () {
                    il.waiter.hide();
                }).success(function() {
                    row.insertAfter(row.next());
                });
        });';
            })
        ]));
        $this->tpl->parseCurrentBlock();

        parent::fillRow($action);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->link()->standard($this->txt("edit_action"), self::dic()->ctrl()
                ->getLinkTargetByClass(ActionGUI::class, ActionGUI::CMD_EDIT_ACTION)),
            self::dic()->ui()->factory()->link()->standard($this->txt("remove_action"), self::dic()->ctrl()
                ->getLinkTargetByClass(ActionGUI::class, ActionGUI::CMD_REMOVE_ACTION_CONFIRM))
        ])->withLabel($this->txt("actions"))));
    }


    /**
     * @inheritDoc
     *
     * @param AbstractAction $action
     */
    protected function getColumnValue(string $column, /*AbstractAction*/ $action, int $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            case "enabled":
                if ($action->isEnabled()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            case "action_description":
                $column = $action->getActionDescription();
                break;

            case "if_description":
                $column = $action->getIfDescription();
                break;

            default:
                $column = htmlspecialchars(Items::getter($action, $column));
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/*: void*/
    {
        $this->addColumn("");

        $this->addColumn("");

        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_action"), self::dic()->ctrl()
            ->getLinkTargetByClass(ActionGUI::class, ActionGUI::CMD_ADD_ACTION)));

        $this->setSelectAllCheckbox(ActionGUI::GET_PARAM_ACTION_ID);
        $this->addMultiCommand(ActionsGUI::CMD_ENABLE_ACTIONS, $this->txt("enable_actions"));
        $this->addMultiCommand(ActionsGUI::CMD_DISABLE_ACTIONS, $this->txt("disable_actions"));
        $this->addMultiCommand(ActionsGUI::CMD_REMOVE_ACTIONS_CONFIRM, $this->txt("remove_actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setData(self::srUserEnrolment()->enrolmentWorkflow()->actions()->getActions($this->parent_obj->getParent()->getStep()->getStepId(), false));
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
        $this->setId(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_actions");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("actions"));
    }
}
