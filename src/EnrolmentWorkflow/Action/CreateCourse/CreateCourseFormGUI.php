<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse;

use ilCheckboxInputGUI;
use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Workflow;
use srag\RequiredData\SrUserEnrolment\Field\AbstractField;
use srag\RequiredData\SrUserEnrolment\Field\Date\DateField;
use srag\RequiredData\SrUserEnrolment\Field\Text\TextField;

/**
 * Class CreateCourseFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateCourseFormGUI extends AbstractActionFormGUI
{

    /**
     * @var CreateCourse
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, CreateCourse $action)
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
                "required_data_from_step_id" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (Step $step) : string {
                            return $step->getTitle();
                        }, self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                            ->enrolmentWorkflow()
                            ->steps()
                            ->getStepById($this->action->getStepId())
                            ->getWorkflowId())),
                    "setTitle"              => $this->txt("required_data_from_step")
                ],
                "selected_workflow_id"       => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (Workflow $workflow) : string {
                            return $workflow->getTitle();
                        }, self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflows()),
                    "setTitle"              => self::plugin()->translate("select_workflow", SelectWorkflowGUI::LANG_MODULE)
                ],
                "move_request"               => [
                    self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                    self::PROPERTY_SUBITEMS => [
                        "move_request_step_id" => [
                            self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                            self::PROPERTY_REQUIRED => true,
                            self::PROPERTY_OPTIONS  => (!empty($this->action->getSelectedWorkflowId()) ? ["" => ""] + array_map(function (Step $step) : string {
                                    return $step->getTitle();
                                }, self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps($this->action->getSelectedWorkflowId())) : []),
                            "setTitle"              => self::plugin()->translate("step", StepsGUI::LANG_MODULE),
                            self::PROPERTY_NOT_ADD  => empty($this->action->getSelectedWorkflowId())
                        ]
                    ]
                ],
                "field_course_title"         => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (AbstractField $field) : string {
                            return $field->getLabel();
                        }, self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->action->getRequiredDataFromStepId(), [
                            TextField::getType()
                        ])),
                    self::PROPERTY_NOT_ADD  => empty($this->action->getRequiredDataFromStepId())
                ],
                "field_course_start"         => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (AbstractField $field) : string {
                            return $field->getLabel();
                        }, self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->action->getRequiredDataFromStepId(), [
                            DateField::getType()
                        ])),
                    self::PROPERTY_NOT_ADD  => empty($this->action->getRequiredDataFromStepId())
                ],
                "field_course_end"           => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (AbstractField $field) : string {
                            return $field->getLabel();
                        }, self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->action->getRequiredDataFromStepId(), [
                            DateField::getType()
                        ])),
                    self::PROPERTY_NOT_ADD  => empty($this->action->getRequiredDataFromStepId())
                ]
            ]
        );
    }
}
