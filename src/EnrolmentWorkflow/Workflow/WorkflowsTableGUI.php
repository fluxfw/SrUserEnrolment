<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class WorkflowsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class WorkflowsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = WorkflowsGUI::LANG_MODULE;


    /**
     * WorkflowsTableGUI constructor
     *
     * @param WorkflowsGUI $parent
     * @param string       $parent_cmd
     */
    public function __construct(WorkflowsGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param Workflow $workflow
     */
    protected function getColumnValue(/*string*/ $column, /*Workflow*/ $workflow, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            case "enabled":
                if ($workflow->isEnabled()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            default:
                $column = Items::getter($workflow, $column);
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
            ],
            "title"   => [
                "id"      => "title",
                "default" => true,
                "sort"    => false,
                "txt"     => $this->txt("title_field")
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
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_workflow"), self::dic()->ctrl()
            ->getLinkTargetByClass(WorkflowGUI::class, WorkflowGUI::CMD_ADD_WORKFLOW)));

        $this->setSelectAllCheckbox(WorkflowGUI::GET_PARAM_WORKFLOW_ID);
        $this->addMultiCommand(WorkflowsGUI::CMD_ENABLE_WORKFLOWS, $this->txt("enable_workflows"));
        $this->addMultiCommand(WorkflowsGUI::CMD_DISABLE_WORKFLOWS, $this->txt("disable_workflows"));
        $this->addMultiCommand(WorkflowsGUI::CMD_REMOVE_WORKFLOWS_CONFIRM, $this->txt("remove_workflows"));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setData(self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflows(false));
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
        $this->setId("srusrenr_workflows");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("workflows"));
    }


    /**
     * @param Workflow $workflow
     */
    protected function fillRow(/*Workflow*/ $workflow)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(WorkflowGUI::class, WorkflowGUI::GET_PARAM_WORKFLOW_ID, $workflow->getWorkflowId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariable("CHECKBOX_POST_VAR", WorkflowGUI::GET_PARAM_WORKFLOW_ID);
        $this->tpl->setVariable("ID", $workflow->getWorkflowId());
        $this->tpl->parseCurrentBlock();

        parent::fillRow($workflow);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->link()->standard($this->txt("edit_workflow"), self::dic()->ctrl()
                ->getLinkTargetByClass(WorkflowGUI::class, WorkflowGUI::CMD_EDIT_WORKFLOW)),
            self::dic()->ui()->factory()->link()->standard($this->txt("remove_workflow"), self::dic()->ctrl()
                ->getLinkTargetByClass(WorkflowGUI::class, WorkflowGUI::CMD_REMOVE_WORKFLOW_CONFIRM))
        ])->withLabel($this->txt("actions"))));
    }
}
