<?php

require_once __DIR__ . "/../vendor/autoload.php";
if (file_exists(__DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php")) {
	require_once __DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php";
}

use srag\DIC\SrUserEnrolment\Util\LibraryLanguageInstaller;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RemovePluginDataConfirm\SrUserEnrolment\PluginUninstallTrait;

/**
 * Class ilSrUserEnrolmentPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentPlugin extends ilUserInterfaceHookPlugin {

	use PluginUninstallTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_ID = "srusrenr";
	const PLUGIN_NAME = "SrUserEnrolment";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = SrUserEnrolmentRemoveDataConfirm::class;
	/**
	 * @var self|null
	 */
	protected static $instance = null;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * ilSrUserEnrolmentPlugin constructor
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * @return string
	 */
	public function getPluginName(): string {
		return self::PLUGIN_NAME;
	}


	/**
	 * @inheritdoc
	 */
	public function updateLanguages($a_lang_keys = null) {
		parent::updateLanguages($a_lang_keys);

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
			. "/../vendor/srag/removeplugindataconfirm/lang")->updateLanguages();
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(Config::TABLE_NAME, false);
		self::dic()->database()->dropTable(Rule::TABLE_NAME, false);
	}
}
