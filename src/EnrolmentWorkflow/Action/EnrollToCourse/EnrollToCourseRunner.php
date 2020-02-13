<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class EnrollToCourseRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
    public function run(Request $request)/*:void*/
    {
        self::srUserEnrolment()->ruleEnrolment()->enrollMemberToCourse($request->getObjId(), $request->getUserId());
    }
}
