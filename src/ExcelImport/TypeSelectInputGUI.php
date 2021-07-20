<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\TextInputGUI\TextInputGUIWithModernAutoComplete;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Version\PluginVersionParameter;

/**
 * Class TypeSelectInputGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport;
 */
class TypeSelectInputGUI extends ilSelectInputGUI
{

    use DICTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
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
    public static function init() : void
    {
        if (self::$init === false) {
            self::$init = true;

            TextInputGUIWithModernAutoComplete::init(self::plugin());

            $version_parameter = PluginVersionParameter::getInstance()->withPlugin(self::plugin());

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl(self::plugin()->directory() . "/js/type_select_input_gui.min.js",
                self::plugin()->directory() . "/js/type_select_input_gui.js"));
        }
    }
}
