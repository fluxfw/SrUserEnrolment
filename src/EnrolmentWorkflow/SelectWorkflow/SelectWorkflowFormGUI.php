<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow;

use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Workflow;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class SelectWorkflowFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SelectWorkflowFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = SelectWorkflowGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * SelectWorkflowFormGUI constructor
     *
     * @param SelectWorkflowGUI $parent
     */
    public function __construct(SelectWorkflowGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        switch ($key) {
            case "workflow_id":
                return self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->getWorkflowId(self::dic()->objDataCache()->lookupObjId($this->parent->getObjRefId()));
                break;

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(SelectWorkflowGUI::CMD_UPDATE_SELECTED_WORKFLOW, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "workflow_id" => [
                self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_OPTIONS  => [0 => ""] + array_map(function (Workflow $workflow) : string {
                        return $workflow->getTitle();
                    }, self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflows()),
                "setTitle"              => self::plugin()->translate("workflow", WorkflowsGUI::LANG_MODULE)
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
        $this->setTitle($this->txt("select_workflow"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value)/*: void*/
    {
        switch ($key) {
            case "workflow_id":
                self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->setWorkflowId(self::dic()->objDataCache()->lookupObjId($this->parent->getObjRefId()),
                    ($value !== "" ? $value : null));
                break;

            default:
                break;
        }
    }
}
