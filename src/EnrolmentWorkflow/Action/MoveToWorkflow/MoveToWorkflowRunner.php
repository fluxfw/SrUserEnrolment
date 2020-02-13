<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class MoveToWorkflowRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MoveToWorkflowRunner extends AbstractActionRunner
{

    /**
     * @var MoveToWorkflow
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(MoveToWorkflow $action)
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
