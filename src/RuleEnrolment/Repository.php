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
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Logs\Repository as LogRepository;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Repository as RuleRepository;
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
     * @var self
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
        $this->logs()->dropTables();
        $this->rules()->dropTables();
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @throws SrUserEnrolmentException
     */
    public function enrollMemberToCourse(int $obj_id, int $user_id)/*: void*/
    {
        $course = ilObjectFactory::getInstanceByObjId($obj_id, false);

        if ($course instanceof ilObjCourse) {

            if ($course->getMembersObject()->isAssigned($user_id)) {
                throw new SrUserEnrolmentException("User " . $user_id . " already assigned as role "
                    . implode(", ", array_map(function (int $role_id) : string {
                        return ilObjRole::_getTranslation(self::dic()->objDataCache()->lookupTitle($role_id));
                    }, $course->getMembersObject()->getAssignedRoles($user_id))) . " in course " . $course->getTitle());
            }

            $course->getMembersObject()->add($user_id, IL_CRS_MEMBER);
        }
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

        $global_roles = self::dic()->rbacreview()->getRolesForIDs(self::dic()->rbacreview()->getGlobalRoles(), false);

        $roles = [];
        foreach ($global_roles as $global_role) {
            $roles[$global_role["rol_id"]] = $global_role["title"];
        }

        return $roles;
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

        if (!self::srUserEnrolment()->userHasRole($user_id)) {
            return false;
        }

        return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {
        $this->logs()->installTables();
        $this->rules()->installTables();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::plugin()->getPluginObject()->isActive() && Config::getField(Config::KEY_SHOW_RULES_ENROLL));
    }


    /**
     * @return LogRepository
     */
    public function logs() : LogRepository
    {
        return LogRepository::getInstance();
    }


    /**
     * @return RuleRepository
     */
    public function rules() : RuleRepository
    {
        return RuleRepository::getInstance();
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
}
