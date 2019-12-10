<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse;

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
        $fields = self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequest($request->getObjRefId(), $this->action->getRequiredDataFromStepId(), $request->getUserId())->getRequiredData();

        $crs = new ilObjCourse();

        $crs->setTitle($fields->{$this->action->getFieldCourseTitle()});

        $crs->setCourseStart($fields->{$this->action->getFieldCourseStart()});

        $crs->setCourseEnd($fields->{$this->action->getFieldCourseEnd()});

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
