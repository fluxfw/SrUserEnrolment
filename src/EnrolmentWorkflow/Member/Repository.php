<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilObjCourse;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
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
     * Repository constructor
     */
    private function __construct()
    {

    }


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
     * @param Member $member
     */
    public function deleteMember(Member $member)/*:void*/
    {
        $member->delete();
    }


    /**
     * @param int $usr_id
     */
    public function deleteUserMembers(int $usr_id)/*: void*/
    {
        foreach (
            Member::where([
                "usr_id" => $usr_id
            ])->get() as $member
        ) {
            $this->deleteMember($member);
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Member::TABLE_NAME, false);
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
     * @param int $usr_id
     *
     * @return Member
     */
    public function getMember(int $obj_ref_id, int $usr_id) : Member
    {
        /**
         * @var Member|null $member
         */

        $obj_id = self::dic()->objDataCache()->lookupObjId($obj_ref_id);

        $member = Member::where([
            "obj_id" => $obj_id,
            "usr_id" => $usr_id
        ])->first();

        if ($member === null) {
            $member = $this->factory()->newInstance();

            $member->setObjRefId($obj_ref_id);

            $member->setObjId($obj_id);

            $member->setUsrId($usr_id);

            $this->storeMember($member);
        }

        return $member;
    }


    /**
     * @param int $obj_ref_id
     *
     * @return Member[]
     */
    public function getMembers(int $obj_ref_id) : array
    {
        $usr_ids = array_map(function (Request $request) : int {
            return $request->getUserId();
        }, self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($obj_ref_id));

        $obj = self::srUserEnrolment()->getIliasObjectByRefId($obj_ref_id);
        if ($obj instanceof ilObjCourse) {
            $usr_ids = array_merge($usr_ids, self::srUserEnrolment()->ruleEnrolment()->getEnrolleds($obj->getId()));
        }

        return array_reduce(array_unique($usr_ids), function (array $members, int $usr_id) use ($obj_ref_id): array {
            $members[$usr_id] = $this->getMember($obj_ref_id, $usr_id);

            return $members;
        }, []);
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id) : bool
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
        Member::updateDB();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::srUserEnrolment()->enrolmentWorkflow()->isEnabled() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_MEMBERS));
    }


    /**
     * @param int      $obj_ref_id
     * @param int      $usr_id
     * @param int|null $time
     *
     * @return Member
     */
    public function setEnrollmentTime(int $obj_ref_id, int $usr_id, /*?*/ int $time = null) : Member
    {
        $member = $this->getMember($obj_ref_id, $usr_id);

        $member->setEnrollmentTime($time);

        $this->storeMember($member);

        return $member;
    }


    /**
     * @param Member $member
     */
    public function storeMember(Member $member)/*:void*/
    {
        $time = time();

        if (empty($member->getMemberId())) {
            $member->setCreatedTime($time);

            $member->setCreatedUserId(self::dic()->user()->getId());
        }

        $member->setUpdatedTime($time);

        $member->setUpdatedUserId(self::dic()->user()->getId());

        $member->store();
    }
}
