<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;

/**
 * Class DeleteRequests
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests
 */
class DeleteRequests extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "delreq";


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        return "";
    }
}
