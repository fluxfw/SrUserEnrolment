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
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MoveToStepFormGUI extends AbstractActionFormGUI
{

    /**
     * @var MoveToStep
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, MoveToStep $object)
    {
        parent::__construct($parent, $object);
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
                "move_to_step_id" => [
                    self::PROPERTY_CLASS    => ilSelectInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_OPTIONS  => ["" => ""] + array_map(function (Step $step) : string {
                            return $step->getTitle();
                        }, array_filter(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                            ->enrolmentWorkflow()
                            ->steps()
                            ->getStepById($this->object->getStepId())
                            ->getWorkflowId()),
                            function (Step $step) : bool {
                                return ($step->getStepId() !== $this->object->getStepId());
                            })),
                    "setTitle"              => self::plugin()->translate("step", StepsGUI::LANG_MODULE)
                ]
            ]);
    }
}
