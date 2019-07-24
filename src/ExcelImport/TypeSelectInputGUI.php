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
class TypeSelectInputGUI extends ilSelectInputGUI {

	use DICTrait;
	/**
	 * @var bool
	 */
	protected static $init = false;


	/**
	 *
	 */
	protected function initJS()/*: void*/ {
		if (self::$init === false) {
			self::$init = true;

			(new TextInputGUIWithModernAutoComplete())->initJS();

			$dir = __DIR__;
			$dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

			self::dic()->mainTemplate()->addJavaScript($dir . "/../../js/type_select_input_gui.min.js");
		}
	}


	/**
	 * @param string $a_mode
	 *
	 * @return string
	 */
	public function render(/*string*/ $a_mode = ""): string {
		$this->initJS();

		return parent::render();
	}
}
