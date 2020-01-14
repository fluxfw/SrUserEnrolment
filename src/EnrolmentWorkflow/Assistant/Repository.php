<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
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
     * @param Assistants $assistants
     */
    public function deleteAssistants(Assistants $assistants)/*:void*/
    {
        $assistants->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Assistants::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $user_id
     *
     * @return Assistants
     */
    public function getAssistantsForUser(int $user_id) : Assistants
    {
        /**
         * @var Assistants|null $assistans
         */

        $assistans = Assistants::where([
            "user_id" => $user_id
        ])->first();

        if ($assistans === null) {
            $assistans = $this->factory()->newInstance();

            $assistans->setUserId($user_id);
        }

        return $assistans;
    }


    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id) : bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return ($user_id !== ANONYMOUS_USER_ID);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Assistants::updateDB();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::srUserEnrolment()->enrolmentWorkflow()->isEnabled() && Config::getField(Config::KEY_SHOW_ASSISTANTS));
    }


    /**
     * @param Assistants $assistants
     */
    public function storeAssistants(Assistants $assistants)/*:void*/
    {
        $assistants->store();
    }
}
