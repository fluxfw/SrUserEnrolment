<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId;

/**
 * Trait RefId
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait RefId
{

    /**
     * @var int
     *
     * @con_has_field     true
     * @con_fieldtype     integer
     * @con_length        8
     * @con_is_notnull    true
     */
    protected $ref_id = 0;


    /**
     * @return int
     */
    public function getRefId() : int
    {
        return $this->ref_id;
    }


    /**
     * @param int $ref_id
     */
    public function setRefId(int $ref_id)/* : void*/
    {
        $this->ref_id = $ref_id;
    }
}
