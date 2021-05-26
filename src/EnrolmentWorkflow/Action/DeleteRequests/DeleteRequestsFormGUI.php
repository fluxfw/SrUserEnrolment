<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;

/**
 * Class DeleteRequestsFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests
 */
class DeleteRequestsFormGUI extends AbstractActionFormGUI
{

    /**
     * @var DeleteRequests
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, DeleteRequests $action)
    {
        parent::__construct($parent, $action);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            []
        );
    }
}
