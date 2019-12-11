<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse;

use ilDate;
use ilObjCourse;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class CreateCourseRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateCourseRunner extends AbstractActionRunner
{

    /**
     * @var CreateCourse
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(CreateCourse $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request) : bool
    {
        if ($request->getStepId() !== $this->action->getRequiredDataFromStepId()) {
            $fields = self::srUserEnrolment()
                ->enrolmentWorkflow()
                ->requests()
                ->getRequest($request->getObjRefId(), $this->action->getRequiredDataFromStepId(), $request->getUserId())
                ->getRequiredData();
        } else {
            $fields = self::srUserEnrolment()->requiredData()->fills()->getFillValues();
        }

        $crs = new ilObjCourse();

        $crs->setTitle($fields[$this->action->getFieldCourseTitle()]);

        $crs->setCourseStart(new ilDate($fields[$this->action->getFieldCourseStart()], IL_CAL_UNIX));

        $crs->setCourseEnd(new ilDate($fields[$this->action->getFieldCourseEnd()], IL_CAL_UNIX));

        $crs->create();

        $crs->createReference();

        $crs->putInTree(self::dic()->tree()->getParentId($request->getObjRefId()));

        self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->setWorkflowId($crs->getId(), $this->action->getSelectedWorkflowId());

        if ($this->action->isMoveRequest()) {
            $request->setObjRefId($this->action->getMoveRequestStepId());
        }

        return true;
    }
}
