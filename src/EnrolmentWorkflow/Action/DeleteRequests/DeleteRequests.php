<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;

/**
 * Class DeleteRequests
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
