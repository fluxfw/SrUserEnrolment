<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Access
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Access
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
     * Access constructor
     */
    private function __construct()
    {

    }


    /**
     * @return bool
     */
    public function currentUserHasRole() : bool
    {
        $user_id = self::dic()->user()->getId();

        $user_roles = self::dic()->rbacreview()->assignedGlobalRoles($user_id);
        $config_roles = Config::getField(Config::KEY_ROLES);

        $ok = false;
        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $config_roles)) {
                $ok = true;
            }
        }
        if (!$ok) {
            return false;
        }

        switch (self::dic()->objDataCache()->lookupType(self::rules()->getObjId())) {
            case "cat":
            case "orgu":
                return self::dic()->access()->checkAccess("cat_administrate_users", "", self::rules()->getRefId());

            case "crs":
            default:
                return self::dic()->access()->checkAccess("write", "", self::rules()->getRefId());
        }
    }
}
