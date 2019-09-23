<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\ActiveRecordConfig\SrUserEnrolment\ActiveRecordConfigGUI;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ilSrUserEnrolmentConfigGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentConfigGUI extends ActiveRecordConfigGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var array
     */
    protected static $tabs
        = [
            self::TAB_CONFIGURATION => ConfigFormGUI::class
        ];
}

ExcelImportGUI::CMD_KEY_AUTOCOMPLETE; // TODO: Fix composer autoload (ilCtrl is lowercase and composer not map lowercase :()
