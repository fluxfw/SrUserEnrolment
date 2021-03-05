<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilSelectInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TextInputGUI\TextInputGUIWithModernAutoComplete;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Version\PluginVersionParameter;

/**
 * Class TypeSelectInputGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport;
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TypeSelectInputGUI extends ilSelectInputGUI
{

    use DICTrait;

    /**
     * @var bool
     */
    protected static $init = false;


    /**
     * TypeSelectInputGUI constructor
     *
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct(string $a_title = "", string $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);

        self::init();
    }


    /**
     *
     */
    public static function init()/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            TextInputGUIWithModernAutoComplete::init(self::plugin());

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            $version_parameter = PluginVersionParameter::getInstance()->withPlugin(self::plugin());

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../js/type_select_input_gui.min.js", $dir . "/../../js/type_select_input_gui.js"));
        }
    }
}
