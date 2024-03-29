<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class DeleteRequestsRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests
 */
class DeleteRequestsRunner extends AbstractActionRunner
{

    /**
     * @var DeleteRequests
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(DeleteRequests $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request) : void
    {
        foreach (self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($request->getObjRefId(), null, [$request->getUserId()]) as $request_) {
            self::srUserEnrolment()->enrolmentWorkflow()->requests()->deleteRequest($request_);
        }
    }
}
