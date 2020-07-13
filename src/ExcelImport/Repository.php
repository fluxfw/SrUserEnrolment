<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilDBConstants;
use ilObjUser;
use ilOrgUnitPosition;
use ilOrgUnitUserAssignment;
use ilSrUserEnrolmentPlugin;
use ilUserAccountSettings;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
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
     * @param stdClass $fields
     *
     * @return int
     *
     * @throws SrUserEnrolmentException
     */
    public function createNewAccount(stdClass $fields) : int
    {
        $user = new ilObjUser();

        $user->setActive(true);

        $user->setTimeLimitUnlimited(true);

        $this->setUserFields($user, $fields);

        $user->create();

        $user->saveAsNew();

        $this->assignRoles($user, $fields);

        $this->assignOrgUnit($user, $fields);

        $this->resetPassword($user, $fields);

        return $user->getId();
    }


    /**
     * @internal
     */
    public function dropTables()/*: void*/
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
     * @param string[] $wheres
     * @param string[] $types
     * @param string[] $values
     * @param string   $additional_joins
     *
     * @return int|null
     */
    public function getObjectRefIdByFilter(array $wheres, array $types, array $values, string $additional_joins = "")/*: ?int*/
    {
        $result = self::srUserEnrolment()->ruleEnrolment()->getObjectFilterStatement($wheres, $types, $values, ["ref_id"], $additional_joins);

        if ($result->rowCount() === 1) {
            return intval($result->fetchAssoc()["ref_id"]);
        } else {
            return null;
        }
    }


    /**
     * @param string $position
     *
     * @return int|null
     */
    public function getPositionIdByTitle(string $position)/*: ?int*/
    {
        /**
         * @var ilOrgUnitPosition|null $position
         */
        $position = ilOrgUnitPosition::where([
            "title" => $position
        ])->first();

        if ($position !== null) {
            return $position->getId();
        } else {
            return null;
        }
    }


    /**
     * @param string $field_name
     *
     * @return int|null
     */
    public function getUserDefinedFieldID(string $field_name)/*: ?int*/
    {
        $result = self::dic()->database()->queryF('SELECT field_id FROM udf_definition WHERE field_name=%s', [ilDBConstants::T_TEXT], [$field_name]);

        if (($row = $result->fetchAssoc()) !== false) {
            return intval($row["field_id"]);
        } else {
            return null;
        }
    }


    /**
     * @param string $email
     *
     * @return int|null
     */
    public function getUserIdByEmail(string $email)/*:?int*/
    {
        $login = current(ilObjUser::getUserLoginsByEmail($email));

        if (!empty($login)) {
            return ilObjUser::_lookupId($login);
        } else {
            return null;
        }
    }


    /**
     * @param string $login
     *
     * @return int|null
     */
    public function getUserIdByLogin(string $login)/*:?int*/
    {
        return ilObjUser::_lookupId($login);
    }


    /**
     * @param int $matriculation_number
     *
     * @return int|null
     */
    public function getUserIdByMatriculationNumber(int $matriculation_number)/*:?int*/
    {
        $result = self::dic()->database()->queryF("SELECT usr_id FROM usr_data WHERE "
            . self::dic()
                ->database()
                ->in("matriculation", array_unique(array_map(function (int $len) use ($matriculation_number) : string {
                    return str_pad($matriculation_number, $len, 0, STR_PAD_LEFT);
                }, range(1, 8))), false,
                    ilDBConstants::T_TEXT) . " AND usr_id>%s",
            [ilDBConstants::T_INTEGER], [6]);

        if (($row = $result->fetchAssoc()) !== false) {
            return $row["usr_id"];
        }

        return null;
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
        $type = ExcelImportGUI::getObjType($obj_ref_id, $obj_single_id);

        if ($type !== "cmps") {
            if (!$this->isEnabled()) {
                return false;
            }

            if (!self::srUserEnrolment()->userHasRole($user_id)) {
                return false;
            }
        }

        switch ($type) {
            case "crs":
                if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_COURSE)) {
                    return false;
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);

            case "cat":
            case "orgu":
                if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER)) {
                    return false;
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "cat_administrate_users", "", $obj_ref_id);

            case "role":
                if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER)) {
                    return false;
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id, null, $obj_single_id);

            case "cmps":
            case "usrf":
            default:
                if ($type !== "cmps") {
                    if (!self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER)) {
                        return false;
                    }
                }

                return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);
        }
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {

    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::plugin()->getPluginObject()->isActive() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT));
    }


    /**
     * @return bool
     */
    public function isLocalUserAdminisrationEnabled() : bool
    {
        return ilUserAccountSettings::getInstance()->isLocalUserAdministrationEnabled();
    }


    /**
     * @param ilObjUser $user
     * @param stdClass  $fields
     *
     * @return int
     *
     * @throws SrUserEnrolmentException
     */
    public function setUserFields(ilObjUser $user, stdClass $fields) : int
    {
        $count = 0;

        $custom_fields = [];

        foreach ($fields as $type => $fields_) {
            foreach ($fields_ as $key => $value) {
                if ((intval($type) === ExcelImport::FIELDS_TYPE_ILIAS && $key === "passwd")
                    || (intval($type) === ExcelImport::FIELDS_TYPE_ILIAS
                        && $key === "org_unit")
                    || (intval($type) === ExcelImport::FIELDS_TYPE_ILIAS && $key === "org_unit_position")
                    || (intval($type) === ExcelImport::FIELDS_TYPE_ILIAS && $key === "roles")
                ) {
                    // Set later
                    continue;
                }
                if (!empty($value)) {
                    switch ($type) {
                        case ExcelImport::FIELDS_TYPE_ILIAS:
                            if (method_exists($user, $method = "set" . Items::strToCamelCase($key))) {
                                Items::setter($user, $key, $value);
                            } else {
                                throw new SrUserEnrolmentException("User default field $key not found!");
                            }
                            break;

                        case ExcelImport::FIELDS_TYPE_CUSTOM:
                            $field_id = $this->getUserDefinedFieldID($key);
                            if (!empty($field_id)) {
                                $custom_fields[$field_id] = $value;
                            } else {
                                throw new SrUserEnrolmentException("User custom field $key not found!");
                            }
                            break;

                        default:
                            break;
                    }

                    $count++;
                }
            }
        }

        $user->setUserDefinedData($custom_fields);

        if (empty($user->getLogin())) {
            throw new SrUserEnrolmentException("Login can't be empty!");
        }

        return $count;
    }


    /**
     * @param int      $user_id
     * @param stdClass $fields
     *
     * @return bool
     *
     * @throws SrUserEnrolmentException
     */
    public function updateUserAccount(int $user_id, stdClass $fields) : bool
    {
        $user = new ilObjUser($user_id);

        unset($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->roles);

        $updated = ($this->setUserFields($user, $fields) > 0);

        if ($updated) {
            $user->update();
        }

        /*if ($this->assignRoles($user, $fields)) {
            $updated = true;
        }*/

        if ($this->assignOrgUnit($user, $fields)) {
            $updated = true;
        }

        if ($this->resetPassword($user, $fields)) {
            $updated = true;
        }

        return $updated;
    }


    /**
     * @param ilObjUser $user
     * @param stdClass  $fields
     *
     * @return bool
     */
    protected function assignOrgUnit(ilObjUser $user, stdClass $fields) : bool
    {
        if (isset($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit) && isset($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit_position)
        ) {
            $assignment = ilOrgUnitUserAssignment::where([
                "user_id"     => $user->getId(),
                "orgu_id"     => $fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit,
                "position_id" => $fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit_position
            ])->first();

            if ($assignment === null) {
                $assignment = new ilOrgUnitUserAssignment();
                $assignment->setUserId($user->getId());
                $assignment->setOrguId($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit);
                $assignment->setPositionId($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->org_unit_position);
                $assignment->store();

                return true;
            }
        }

        return false;
    }


    /**
     * @param ilObjUser $user
     * @param stdClass  $fields
     *
     * @return bool
     */
    protected function assignRoles(ilObjUser $user, stdClass $fields) : bool
    {
        $changed = false;

        if (!empty($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->roles)) {
            foreach ($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->roles as $role) {
                self::srUserEnrolment()->ruleEnrolment()->enroll($role, $user->getId());
                $changed = true;
            }
        } else {
            self::srUserEnrolment()->ruleEnrolment()->enroll(ExcelImportFormGUI::USER_ROLE_ID, $user->getId()); // User default role
            $changed = true;
        }

        return $changed;
    }


    /**
     * @param ilObjUser $user
     * @param stdClass  $fields
     *
     * @return bool
     */
    protected function resetPassword(ilObjUser $user, stdClass $fields) : bool
    {
        if (isset($fields->{ExcelImport::FIELDS_TYPE_ILIAS}->passwd)) {
            $fields->{ExcelImport::FIELDS_TYPE_ILIAS}->passwd = self::srUserEnrolment()->resetUserPassword()->resetPassword($user->getId(), $fields->{ExcelImport::FIELDS_TYPE_ILIAS}->passwd);

            return true;
        }

        return false;
    }
}
