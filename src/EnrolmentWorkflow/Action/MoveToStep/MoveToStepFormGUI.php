<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToStep;

use ilSelectInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;

/**
 * Class MoveToStepFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToStep
 */
class MoveToStepFormGUI extends AbstractActionFormGUI
{

    /**
     * @var MoveToStep
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, MoveToStep $action)
    {
        parent::__construct($parent, $action);
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            [
                "move_to_step_id" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (Step $step) : string {
                            return $step->getTitle();
                        }, array_filter(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                            ->enrolmentWorkflow()
                            ->steps()
                            ->getStepById($this->action->getStepId())
                            ->getWorkflowId()),
                            function (Step $step) : bool {
                                return ($step->getStepId() !== $this->action->getStepId());
                            })),
                    "setTitle"              => self::plugin()->translate("step", StepsGUI::LANG_MODULE)
                ]
            ]);
    }
}
