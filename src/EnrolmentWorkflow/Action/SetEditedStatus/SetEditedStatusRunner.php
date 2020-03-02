<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class SetEditedStatusRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SetEditedStatusRunner extends AbstractActionRunner
{

    /**
     * @var SetEditedStatus
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(SetEditedStatus $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request)/*:void*/
    {
        $requested_group = $request->getRequestGroup();

        $requested_group->setEditedStatus($this->action->getEditedStatus());

        self::srUserEnrolment()->enrolmentWorkflow()->requests()->storeRequestGroup($requested_group);
    }
}
