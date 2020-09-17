<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings
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
     * @var Settings[]
     */
    protected $settings = [];


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
        self::dic()->database()->dropTable(Settings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_id
     *
     * @return Settings
     */
    public function getSettings(int $obj_id) : Settings
    {
        $settings = $this->settings[$obj_id];

        if ($settings === null) {
            $settings = Settings::where(["obj_id" => $obj_id])->first();

            if ($settings === null) {
                $settings = $this->factory()->newInstance();

                $settings->setObjId($obj_id);
            }

            $this->settings[$obj_id] = $settings;
        }

        return $settings;
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {
        Settings::updateDB();
    }


    /**
     * @param Settings $settings
     */
    public function storeSettings(Settings $settings)/* : void*/
    {
        $settings->store();
    }
}
