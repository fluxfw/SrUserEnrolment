<?php

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ilSrUserEnrolmentUIHookGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentUIHookGUI extends ilUIHookPluginGUI {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const PAR_TABS = "tabs";


	/**
	 * ilSrUserEnrolmentUIHookGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 */
	public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = [])/*: void*/ {

		if ($a_part === self::PAR_TABS) {

			if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)) {

				if (self::access()->currentUserHasRole()) {

					self::dic()->tabs()->addSubTab("","","");
				}
			}
		}
	}
}
