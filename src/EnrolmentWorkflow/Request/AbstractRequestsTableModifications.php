<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRequestsTableModifications
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 */
abstract class AbstractRequestsTableModifications
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    /**
     * AbstractRequestsTableModifications constructor
     */
    public function __construct()
    {

    }


    /**
     * @param Request[] $requests
     * @param array     $filter_values
     *
     * @return array
     */
    public abstract function extendsAndFilterData(array $requests, array $filter_values) : array;


    /**
     * @param string  $column
     * @param Request $request
     *
     * @return string|null
     */
    public abstract function formatColumnValue(string $column, Request $request)/*:?string*/ ;


    /**
     * @param int $requests_type
     *
     * @return array
     */
    public abstract function getAdditionalColumns(int $requests_type) : array;


    /**
     * @param int $requests_type
     *
     * @return array
     */
    public abstract function getAdditionalFilterFields(int $requests_type) : array;
}
