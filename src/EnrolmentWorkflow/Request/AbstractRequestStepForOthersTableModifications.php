<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilObjUser;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRequestStepForOthersTableModifications
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 */
abstract class AbstractRequestStepForOthersTableModifications
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    /**
     * AbstractRequestStepForOthersTableModifications constructor
     */
    public function __construct()
    {

    }


    /**
     * @param ilObjUser[] $users
     * @param array       $filter_values
     *
     * @return array
     */
    public abstract function extendsAndFilterData(array $users, array $filter_values) : array;


    /**
     * @param string    $column
     * @param ilObjUser $user
     *
     * @return string|null
     */
    public abstract function formatColumnValue(string $column, ilObjUser $user) : ?string;


    /**
     * @return array
     */
    public abstract function getAdditionalColumns() : array;


    /**
     * @return array
     */
    public abstract function getAdditionalFilterFields() : array;
}
