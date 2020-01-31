<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDFFormGUI;

/**
 * Class UDFSupervisorFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
