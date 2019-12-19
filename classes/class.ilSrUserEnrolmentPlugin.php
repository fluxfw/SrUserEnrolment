<?php

require_once __DIR__ . "/../vendor/autoload.php";
if (file_exists(__DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php")) {
    require_once __DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php";
}

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\DIC\SrUserEnrolment\Util\LibraryLanguageInstaller;
use srag\Plugins\SrUserEnrolment\Menu\Menu;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RemovePluginDataConfirm\SrUserEnrolment\PluginUninstallTrait;

/**
 * Class ilSrUserEnrolmentPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_ID = "srusrenr";
    const PLUGIN_NAME = "SrUserEnrolment";
    const PLUGIN_CLASS_NAME = self::class;
    const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = SrUserEnrolmentRemoveDataConfirm::class;
    const EVENT_AFTER_REQUEST = "after_request";
    const EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS = "collect_requests_table_modifications";
    const EVENT_EXTENDS_SRUSRENR = "extends_" . self::PLUGIN_ID;
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
     * ilSrUserEnrolmentPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return string
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritdoc
     */
    public function updateLanguages($a_lang_keys = null)
    {
        parent::updateLanguages($a_lang_keys);

        LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
            . "/../vendor/srag/removeplugindataconfirm/lang")->updateLanguages();

        self::srUserEnrolment()->notifications4plugin()->installLanguages();

        self::srUserEnrolment()->requiredData()->installLanguages();
    }


    /**
     * @inheritdoc
     */
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider
    {
        return new Menu(self::dic()->dic(), $this);
    }


    /**
     * @inheritdoc
     */
    protected function deleteData()/*: void*/
    {
        self::srUserEnrolment()->dropTables();
    }
}
