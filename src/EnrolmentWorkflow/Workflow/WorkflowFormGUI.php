<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class WorkflowFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class WorkflowFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = WorkflowsGUI::LANG_MODULE;
    /**
     * @var Workflow
     */
    protected $workflow;


    /**
     * WorkflowFormGUI constructor
     *
     * @param WorkflowGUI $parent
     * @param Workflow    $workflow
     */
    public function __construct(WorkflowGUI $parent, Workflow $workflow)
    {
        $this->workflow = $workflow;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return Items::getter($this->workflow, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        if (!empty($this->workflow->getWorkflowId())) {
            $this->addCommandButton(WorkflowGUI::CMD_UPDATE_WORKFLOW, $this->txt("save"));
        } else {
            $this->addCommandButton(WorkflowGUI::CMD_CREATE_WORKFLOW, $this->txt("add"));
            $this->addCommandButton(WorkflowGUI::CMD_BACK, $this->txt("cancel"));
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "enabled" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "title"   => [
                self::PROPERTY_CLASS    => ilTextInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                "setTitle"              => $this->txt("title_field")
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
        $this->setTitle($this->txt(!empty($this->workflow->getWorkflowId()) ? "edit_workflow" : "add_workflow"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->workflow, $key, $value);
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

        self::srUserEnrolment()->enrolmentWorkflow()->workflows()->storeWorkflow($this->workflow);

        return true;
    }
}
