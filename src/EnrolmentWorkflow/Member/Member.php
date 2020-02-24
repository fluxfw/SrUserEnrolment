<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilObject;
use ilObjectFactory;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Member
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Member
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var int
     */
    protected $usr_id;


    /**
     * Member constructor
     *
     * @param int $obj_ref_id
     * @param int $usr_id
     */
    public function __construct(int $obj_ref_id, int $usr_id)
    {
        $this->obj_ref_id = $obj_ref_id;
        $this->usr_id = $usr_id;
    }


    /**
     * @return ilObject
     */
    public function getObject() : ilObject
    {
        return ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);
    }


    /**
     * @return ilObjUser
     */
    public function getUser() : ilObjUser
    {
        return new ilObjUser($this->usr_id);
    }


    /**
     * @return Request|null
     */
    public function getRequest()/*:Request|null*/
    {
        return end(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($this->obj_ref_id, null, $this->usr_id));
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }
}