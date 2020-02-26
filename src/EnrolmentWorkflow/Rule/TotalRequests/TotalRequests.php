<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class TotalRequests
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TotalRequests extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "totareq";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_is_notnull   true
     */
    protected $total_requests = 0;


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
                return false;

            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return $this->getTotalRequests();
    }


    /**
     * @return int
     */
    public function getTotalRequests() : int
    {
        return $this->total_requests;
    }


    /**
     * @param int $total_requests
     */
    public function setTotalRequests(int $total_requests)/* : void*/
    {
        $this->total_requests = $total_requests;
    }
}
