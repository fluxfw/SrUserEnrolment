<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractAssistantsRequestTableModifications
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractAssistantsRequestTableModifications
{

    use DICTrait;
    use SrUserEnrolmentTrait;


    /**
     * AbstractAssistantsRequestTableModifications constructor
     */
    public function __construct()
    {

    }


    /**
     * @return array
     */
    public abstract function getAdditionalColumns() : array;


    /**
     * @return array
     */
    public abstract function getAdditionalFilterFields() : array;


    /**
     * @param Assistant[] $assistants
     * @param array       $filter_values
     */
    public abstract function extendsAndFilterData(array &$assistants, array $filter_values)/*:void*/ ;


    /**
     * @param string    $column
     * @param Assistant $assistant
     *
     * @return string|null
     */
    public abstract function formatColumnValue(string $column, Assistant $assistant)/*:?string*/ ;
}
