<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDFFormGUI;

/**
 * Class UDFSupervisorFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor
 */
class UDFSupervisorFormGUI extends UDFFormGUI
{

    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, UDFSupervisor $rule)
    {
        parent::__construct($parent, $rule);
    }
}
