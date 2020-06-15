<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilSelectInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TextInputGUI\TextInputGUIWithModernAutoComplete;
use srag\DIC\SrUserEnrolment\DICTrait;

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

            TextInputGUIWithModernAutoComplete::init();

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../js/type_select_input_gui.min.js");
        }
    }
}
