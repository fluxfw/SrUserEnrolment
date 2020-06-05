<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment;

use ilDBConstants;
use ilDBStatement;
use ilObjCourse;
use ilObjectFactory;
use ilObjRole;
use ilObjUser;
use ilOrgUnitPosition;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Repository as RulesRepository;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment
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
    public function dropTables()/*: void*/
    {
        $this->rules()->dropTables();
    }


    /**
     * @param int      $obj_id
     * @param int      $user_id
     * @param int|null $type
     *
     * @return bool
     */
    public function enroll(int $obj_id, int $user_id, /*?*/ int $type = null) : bool
    {
        $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

        switch (true) {
            case ($obj instanceof ilObjCourse):
                if (!$obj->getMembersObject()->isAssigned($user_id)) {
                    switch ($type) {
                        case Member::TYPE_ADMIN:
                            $role = IL_CRS_ADMIN;
                            break;

                        case Member::TYPE_TUTOR:
                            $role = IL_CRS_TUTOR;
                            break;

                        case Member::TYPE_MEMBER:
                        default:
                            $role = IL_CRS_MEMBER;
                            break;
                    }

                    $obj->getMembersObject()->add($user_id, $role);

                    return true;
                }
                break;

            case ($obj instanceof ilObjRole):
                if (!self::dic()->rbac()->review()->isAssigned($user_id, $obj->getId())) {
                    if (self::dic()->rbac()->admin()->assignUser($obj->getId(), $user_id)) {
                        return true;
                    }
                }
                break;

            default:
                break;
        }

        return false;
    }


    /**
     * @return array
     */
    public function getAllRoles() : array
    {
        /**
         * @var array $global_roles
         * @var array $roles
         */

        $global_roles = self::dic()->rbac()->review()->getRolesForIDs(self::dic()->rbac()->review()->getGlobalRoles(), false);

        $roles = [];
        foreach ($global_roles as $global_role) {
            $roles[$global_role["rol_id"]] = $global_role["title"];
        }

        return $roles;
    }


    /**
     * @param int $obj_id
     *
     * @return int[]
     */
    public function getEnrolleds(int $obj_id) : array
    {
        $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

        switch (true) {
            case ($obj instanceof ilObjCourse):
                return $obj->getMembersObject()->getParticipants();

            case ($obj instanceof ilObjRole):
                return self::dic()->rbac()->review()->assignedUsers($obj->getId());

            default:
                break;
        }

        return [];
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return int|null
     */
    public function getEnrolledType(int $obj_id, int $user_id)/* : ?int*/
    {
        $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

        switch (true) {
            case ($obj instanceof ilObjCourse):
                if ($obj->getMembersObject()->isAdmin($user_id)) {
                    return Member::TYPE_ADMIN;
                }

                if ($obj->getMembersObject()->isTutor($user_id)) {
                    return Member::TYPE_TUTOR;
                }

                if ($obj->getMembersObject()->isMember($user_id)) {
                    return Member::TYPE_MEMBER;
                }
                break;

            case ($obj instanceof ilObjRole):
            default:
                break;
        }

        return null;
    }


    /**
     * @param string[] $wheres
     * @param string[] $types
     * @param string[] $values
     * @param string[] $selects
     * @param string   $additional_joins
     *
     * @return ilDBStatement
     */
    public function getObjectFilterStatement(array $wheres, array $types, array $values, array $selects, string $additional_joins = "") : ilDBStatement
    {
        return self::dic()->database()->queryF('SELECT ' . implode(', ', $selects)
            . ' FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id ' . $additional_joins . ' WHERE '
            . implode(' AND ', $wheres), $types, $values);
    }


    /**
     * @return array
     */
    public function getPositions() : array
    {
        return array_map(function (ilOrgUnitPosition $position) : string {
            return $position->getTitle();
        }, ilOrgUnitPosition::get());
    }


    /**
     * @return string[]
     */
    public function getUsers() : array
    {
        $result = self::dic()->database()->queryF('SELECT usr_id,login FROM usr_data WHERE usr_id!=%s', [ilDBConstants::T_INTEGER], [ANONYMOUS_USER_ID]);

        $array = [];

        while (($row = $result->fetchAssoc()) !== false) {
            $array[$row["usr_id"]] = $row["login"];
        }

        return $array;
    }


    /**
     * @param int      $user_id
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id, /*?*/ int $obj_single_id = null) : bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (!self::srUserEnrolment()->userHasRole($user_id)) {
            return false;
        }

        switch (RulesCourseGUI::getObjType($obj_ref_id, $obj_single_id)) {
            case "crs":
                if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_RULES_ENROLL_COURSE)) {
                    return false;
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);

            case "role":
            default:
                if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_RULES_ENROLL_USER)) {
                    return false;
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id, null, $obj_single_id);
        }
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {
        $this->rules()->installTables();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::plugin()->getPluginObject()->isActive() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_RULES_ENROLL));
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return int|null
     */
    public function isEnrolled(int $obj_id, int $user_id) : bool
    {
        $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

        switch (true) {
            case ($obj instanceof ilObjCourse):
                return $obj->getMembersObject()->isAssigned($user_id);

            case ($obj instanceof ilObjRole):
                return self::dic()->rbac()->review()->isAssigned($user_id, $obj->getId());

            default:
                break;
        }

        return false;
    }


    /**
     * @return RulesRepository
     */
    public function rules() : RulesRepository
    {
        return RulesRepository::getInstance();
    }


    /**
     * @param string|null $search
     *
     * @return array
     */
    public function searchUsers(/*?*/ string $search = null) : array
    {
        $users = [];

        foreach (ilObjUser::searchUsers($search) as $user) {
            $users[$user["usr_id"]] = $user["firstname"] . " " . $user["lastname"] . " (" . $user["login"] . ")";
        }

        return $users;
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return bool
     */
    public function unenroll(int $obj_id, int $user_id) : bool
    {
        $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

        switch (true) {
            case ($obj instanceof ilObjCourse) :
                if ($obj->getMembersObject()->isAssigned($user_id)) {
                    $obj->getMembersObject()->delete($user_id);

                    return true;
                }
                break;

            case ($obj instanceof ilObjRole):
                if (self::dic()->rbac()->review()->isAssigned($user_id, $obj->getId())) {
                    if (self::dic()->rbac()->admin()->deassignUser($obj->getId(), $user_id)) {
                        return true;
                    }
                }
                break;

            default:
                break;
        }

        return false;
    }
}
