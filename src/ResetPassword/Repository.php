<?php

namespace srag\Plugins\SrUserEnrolment\ResetPassword;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\ResetPassword
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
     * @internal
     */
    public function dropTables()/*: void*/
    {

    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     * @param int $member_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id, int $member_id) : bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (!self::srUserEnrolment()->userHasRole($user_id)) {
            return false;
        }

        if (!self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id)) {
            return false;
        }

        return (self::srUserEnrolment()->ruleEnrolment()->getEnrolledType(self::dic()->objDataCache()->lookupObjId($obj_ref_id), $member_id) === Member::TYPE_MEMBER);
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
        return (self::plugin()->getPluginObject()->isActive() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_RESET_PASSWORD));
    }


    /**
     * @param int         $user_id
     * @param string|null $new_password
     *
     * @return string
     */
    public function resetPassword(int $user_id, /*?*/ string $new_password = null) : string
    {
        $user = self::srUserEnrolment()->getIliasObjectById($user_id);

        if ($new_password === null) {
            $new_password = current(ilUtil::generatePasswords(1));
        }

        $user->resetPassword($new_password, $new_password);

        return $new_password;
    }
}
