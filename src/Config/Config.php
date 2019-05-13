<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\ActiveRecordConfig;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Config
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Config extends ActiveRecordConfig {

	use SrUserEnrolmentTrait;
	const TABLE_NAME = "ui_uihk_srusrenr_config_n";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	/**
	 * @var array
	 */
	protected static $fields = [];
}
