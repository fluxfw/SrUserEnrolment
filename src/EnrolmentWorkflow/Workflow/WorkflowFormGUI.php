<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class WorkflowFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class WorkflowFormGUI extends ObjectPropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = WorkflowsGUI::LANG_MODULE;
    /**
     * @var Workflow
     */
    protected $object;


    /**
     * WorkflowFormGUI constructor
     *
     * @param WorkflowGUI $parent
     * @param Workflow    $object
     */
    public function __construct(WorkflowGUI $parent, Workflow $object)
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
        if (!empty($this->object->getWorkflowId())) {
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
        $this->setTitle($this->txt(!empty($this->object->getWorkflowId()) ? "edit_workflow" : "add_workflow"));
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
