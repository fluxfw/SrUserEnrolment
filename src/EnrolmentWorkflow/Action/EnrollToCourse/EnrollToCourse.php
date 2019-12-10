<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;

/**
 * Class EnrollToCourse
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EnrollToCourse extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "enrlcrs";


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        return "";
    }
}
