<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RemovePluginDataConfirm\SrUserEnrolment\AbstractRemovePluginDataConfirm;

/**
 * Class SrUserEnrolmentRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy SrUserEnrolmentRemoveDataConfirm: ilUIPluginRouterGUI
 */
class SrUserEnrolmentRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
}
