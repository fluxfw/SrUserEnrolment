<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToStep;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class MoveToStepRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToStep
 */
class MoveToStepRunner extends AbstractActionRunner
{

    /**
     * @var MoveToStep
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(MoveToStep $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request)/*:void*/
    {
        $request->setStepId($this->action->getMoveToStepId());
    }
}
