<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow;

use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Workflow;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;

/**
 * Class MoveToWorkflowFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow
 */
class MoveToWorkflowFormGUI extends AbstractActionFormGUI
{

    /**
     * @var MoveToWorkflow
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, MoveToWorkflow $action)
    {
        parent::__construct($parent, $action);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            [
                "move_to_workflow_id" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (Workflow $workflow) : string {
                            return $workflow->getTitle();
                        }, array_filter(self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflows(), function (Workflow $workflow) : bool {
                            return ($workflow->getWorkflowId() !== self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($this->action->getStepId())->getWorkflowId());
                        })),
                    "setTitle"              => self::plugin()->translate("workflow", WorkflowsGUI::LANG_MODULE)
                ],
                "move_to_step_id"     => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => (!empty($this->action->getMoveToWorkflowId()) ? ["" => ""] + array_map(function (Step $step) : string {
                            return $step->getTitle();
                        }, self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps($this->action->getMoveToWorkflowId())) : []),
                    "setTitle"              => self::plugin()->translate("step", StepsGUI::LANG_MODULE),
                    self::PROPERTY_NOT_ADD  => empty($this->action->getMoveToWorkflowId())
                ]
            ]);
    }
}
