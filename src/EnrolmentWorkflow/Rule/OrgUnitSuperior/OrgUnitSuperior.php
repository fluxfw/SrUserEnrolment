<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\Position;

/**
 * Class OrgUnitSuperior
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitSuperior extends AbstractRule
{

    use Position;
    const TABLE_NAME_SUFFIX = "orgsup";


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return htmlspecialchars($this->getPositionTitle());
    }
}
