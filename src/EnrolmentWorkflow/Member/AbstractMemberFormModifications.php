<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractMemberFormModifications
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 */
abstract class AbstractMemberFormModifications
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    /**
     * AbstractMemberFormModifications constructor
     */
    public function __construct()
    {

    }


    /**
     * @return array
     */
    public abstract function getAdditionalFields() : array;


    /**
     * @param Member $member
     * @param string $key
     *
     * @return mixed
     */
    public abstract function getValue(Member $member, string $key);


    /**
     * @param Member $member
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public abstract function storeValue(Member $member, string $key, $value) : bool;


    /**
     * @param MemberFormGUI $form
     *
     * @return bool
     */
    public abstract function validateAdditionals(MemberFormGUI $form) : bool;
}
