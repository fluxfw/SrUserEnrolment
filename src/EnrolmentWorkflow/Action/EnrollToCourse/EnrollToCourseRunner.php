<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class EnrollToCourseRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse
 */
class EnrollToCourseRunner extends AbstractActionRunner
{

    /**
     * @var EnrollToCourse
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(EnrollToCourse $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request) : void
    {
        self::srUserEnrolment()->ruleEnrolment()->enroll($request->getObjId(), $request->getUserId());
    }
}
