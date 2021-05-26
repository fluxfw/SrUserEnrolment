<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractMembersTableModifications
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 */
abstract class AbstractMembersTableModifications
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    /**
     * AbstractMembersTableModifications constructor
     */
    public function __construct()
    {

    }


    /**
     * @param Member[] $members
     * @param array    $filter_values
     *
     * @return array
     */
    public abstract function extendsAndFilterData(array $members, array $filter_values) : array;


    /**
     * @param string $column
     * @param Member $member
     *
     * @return string|null
     */
    public abstract function formatColumnValue(string $column, Member $member)/*:?string*/ ;


    /**
     * @return array
     */
    public abstract function getAdditionalColumns() : array;


    /**
     * @return array
     */
    public abstract function getAdditionalFilterFields() : array;
}
