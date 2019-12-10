<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\CustomInputGUIs\SrUserEnrolment\Waiter\Waiter;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class StepsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class StepsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = StepsGUI::LANG_MODULE;


    /**
     * StepsTableGUI constructor
     *
     * @param StepsGUI $parent
     * @param string   $parent_cmd
     */
    public function __construct(StepsGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param Step $step
     */
    protected function getColumnValue(/*string*/ $column, /*Step*/ $step, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            case "enabled":
                if ($step->isEnabled()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            default:
                $column = Items::getter($step, $column);
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
            "enabled"             => [
                "id"      => "enabled",
                "default" => true,
                "sort"    => false
            ],
            "title"               => [
                "id"      => "title",
                "default" => true,
                "sort"    => false
            ],
            "action_title"        => [
                "id"      => "action_title",
                "default" => true,
                "sort"    => false
            ],
            "action_accept_title" => [
                "id"      => "action_accept_title",
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

        $this->addColumn("");

        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_step"), self::dic()->ctrl()
            ->getLinkTargetByClass(StepGUI::class, StepGUI::CMD_ADD_STEP)));

        $this->setSelectAllCheckbox(StepGUI::GET_PARAM_STEP_ID);
        $this->addMultiCommand(StepsGUI::CMD_ENABLE_STEPS, $this->txt("enable_steps"));
        $this->addMultiCommand(StepsGUI::CMD_DISABLE_STEPS, $this->txt("disable_steps"));
        $this->addMultiCommand(StepsGUI::CMD_REMOVE_STEPS_CONFIRM, $this->txt("remove_steps"));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setData(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps($this->parent_obj->getParent()->getWorkflow()->getWorkflowId(), false));
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
        $this->setId("srusrenr_steps");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("steps"));
    }


    /**
     * @param Step $step
     */
    protected function fillRow(/*Step*/ $step)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(StepGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariable("CHECKBOX_POST_VAR", StepGUI::GET_PARAM_STEP_ID);
        $this->tpl->setVariable("ID", $step->getStepId());
        $this->tpl->parseCurrentBlock();

        $this->tpl->setCurrentBlock("column");
        $this->tpl->setVariable("COLUMN", self::output()->getHTML([
            self::dic()->ui()->factory()->glyph()->sortAscending()->withAdditionalOnLoadCode(function (string $id) : string {
                Waiter::init(Waiter::TYPE_WAITER);

                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                    url: ' . json_encode(self::dic()
                        ->ctrl()
                        ->getLinkTargetByClass(StepGUI::class, StepGUI::CMD_MOVE_STEP_UP, "", true)) . ',
                    type: "GET"
                 }).always(function () {
                    il.waiter.hide();
               }).success(function() {
                    row.insertBefore(row.prev());
                });
            });';
            }),
            self::dic()->ui()->factory()->glyph()->sortDescending()->withAdditionalOnLoadCode(function (string $id) : string {
                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                    url: ' . json_encode(self::dic()
                        ->ctrl()
                        ->getLinkTargetByClass(StepGUI::class, StepGUI::CMD_MOVE_STEP_DOWN, "", true)) . ',
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

        parent::fillRow($step);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->button()->shy($this->txt("edit_step"), self::dic()->ctrl()
                ->getLinkTargetByClass(StepGUI::class, StepGUI::CMD_EDIT_STEP)),
            self::dic()->ui()->factory()->button()->shy($this->txt("remove_step"), self::dic()->ctrl()
                ->getLinkTargetByClass(StepGUI::class, StepGUI::CMD_REMOVE_STEP_CONFIRM))
        ])->withLabel($this->txt("actions"))));
    }
}
