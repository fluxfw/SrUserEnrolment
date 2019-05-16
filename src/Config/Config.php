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
	const TABLE_NAME = "srusrenr_config";
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const KEY_ROLES = "roles";
	const KEY_LAST_LOG_ID_REQUEST_PREFIX = "last_log_id_request_prefix";
	/**
	 * @var array
	 */
	protected static $fields = [
		self::KEY_LAST_LOG_ID_REQUEST_PREFIX => [ self::TYPE_INTEGER, 0 ],
		self::KEY_ROLES => [ self::TYPE_JSON, [] ]
	];
}
