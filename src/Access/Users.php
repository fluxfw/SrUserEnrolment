<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilObjUser;
use ilSrUserEnrolmentPlugin;
use ilUserAccountSettings;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Users
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Users
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
     * Users constructor
     */
    private function __construct()
    {

    }


    /**
     * @param array $fields
     *
     * @return int
     *
     * @throws SrUserEnrolmentException
     */
    public function createNewAccount(array $fields) : int
    {
        $user = new ilObjUser();

        $user->setActive(true);

        $user->setTimeLimitUnlimited(true);

        $this->setUserFields($user, $fields);

        $user->create();

        $user->saveAsNew();

        self::dic()->rbacadmin()->assignUser(4, $user->getId()); // User default role

        return $user->getId();
    }


    /**
     * @param ilObjUser $user
     * @param array     $fields
     *
     * @return int
     *
     * @throws SrUserEnrolmentException
     */
    public function setUserFields(ilObjUser $user, array $fields) : int
    {
        $count = 0;

        $custom_fields = [];

        foreach ($fields as $type => $fields_) {
            foreach ($fields_ as $key => $value) {
                if (($type === ExcelImport::FIELDS_TYPE_ILIAS && $key === "passwd")
                    || ($type === ExcelImport::FIELDS_TYPE_ILIAS
                        && $key === "org_unit")
                    || ($type === ExcelImport::FIELDS_TYPE_ILIAS && $key === "org_unit_position")
                ) {
                    // Set later
                    continue;
                }
                if (!empty($value)) {
                    switch ($type) {
                        case ExcelImport::FIELDS_TYPE_ILIAS:
                            if (method_exists($user, $method = "set" . Items::strToCamelCase($key))) {
                                $user->{$method}($value);
                            } else {
                                throw new SrUserEnrolmentException("User default field $key not found!");
                            }
                            break;

                        case ExcelImport::FIELDS_TYPE_CUSTOM:
                            $field_id = $this->getUserDefinedFieldID($key);
                            if (!empty($field_id)) {
                                $custom_fields [$field_id] = $value;
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
     * @return int
     */
    public function getUserId() : int
    {
        $user_id = self::dic()->user()->getId();

        // Fix login screen
        if ($user_id === 0 && boolval(self::dic()->settings()->get("pub_section"))) {
            $user_id = ANONYMOUS_USER_ID;
        }

        return intval($user_id);
    }


    /**
     * @return ilObjUser[]
     */
    public function getUsers() : array
    {
        $result = self::dic()->database()->query('SELECT usr_id FROM usr_data');

        $array = [];

        while (($row = $result->fetchAssoc()) !== false) {
            $array[] = new ilObjUser($row["usr_id"]);
        }

        return $array;
    }


    /**
     * @param string $key
     *
     * @return int|null
     */
    protected function getUserDefinedFieldID(string $key)/*: ?int*/
    {
        $result = self::dic()->database()->queryF('SELECT field_id FROM udf_definition WHERE field_name=%s', ["text"], [$key]);

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
        $login = current(self::version()->is54() ? ilObjUser::getUserLoginsByEmail($email) : ilObjUser::_getUserIdsByEmail($email));

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
     * @return bool
     */
    public function isLocalUserAdminisrationEnabled() : bool
    {
        return ilUserAccountSettings::getInstance()->isLocalUserAdministrationEnabled();
    }


    /**
     * @param int         $user_id
     * @param string|null $new_password
     *
     * @return string
     */
    public function resetPassword(int $user_id, /*?string*/ $new_password = null) : string
    {
        $user = new ilObjUser($user_id);

        if ($new_password === null) {
            $new_password = current(ilUtil::generatePasswords(1));
        }

        $user->resetPassword($new_password, $new_password);

        return $new_password;
    }


    /**
     * @param int   $user_id
     * @param array $fields
     *
     * @return bool
     *
     * @throws SrUserEnrolmentException
     */
    public function updateUserAccount(int $user_id, array $fields) : bool
    {
        $user = new ilObjUser($user_id);

        $updated = ($this->setUserFields($user, $fields) > 0);

        if ($updated) {
            $user->update();
        }

        return $updated;
    }
}
