<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Members;

use ilObjCourse;
use ilObjectFactory;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Members
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {

    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_ref_id
     *
     * @return Member[]
     */
    public function getMembers(int $obj_ref_id) : array
    {
        $usr_ids = [];

        $obj = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);

        if ($obj instanceof ilObjCourse) {
            $usr_ids = array_merge($usr_ids, $obj->getMembersObject()->getMembers());
        }

        $usr_ids = array_merge($usr_ids, array_map(function (Request $request) : int {
            return $request->getUserId();
        }, self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($obj_ref_id)));

        return array_map(function (int $usr_id) use ($obj_ref_id) : Member {
            return self::srUserEnrolment()->enrolmentWorkflow()->members()->factory()->newInstance($obj_ref_id, $usr_id);
        }, array_unique($usr_ids));
    }


    /**
     * @param int $obj_ref_id
     * @param int $user_id
     *
     * @return bool
     */
    public function hasAccess(int $obj_ref_id, int $user_id) : bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if ($user_id === ANONYMOUS_USER_ID) {
            return false;
        }

        return self::dic()->access()->checkAccessOfUser(self::dic()->user()->getId(), "write", "", $obj_ref_id);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {

    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::srUserEnrolment()->enrolmentWorkflow()->isEnabled() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_MEMBERS));
    }
}
