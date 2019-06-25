<?php

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Rule\RulesGUI;
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
	const PAR_SUB_TABS = "sub_tabs";


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

		if ($a_part === self::PAR_SUB_TABS) {

			if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
				|| self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseParticipantsGroupsGUI::class)
				|| self::dic()->ctrl()->getCmdClass() === strtolower(ilUsersGalleryGUI::class)) {

				if (self::access()->currentUserHasRole()) {

					self::dic()->ctrl()->setParameterByClass(RulesGUI::class, Repository::GET_PARAM_REF_ID, self::rules()->getRefId());

					self::dic()->tabs()->addSubTab(RulesGUI::TAB_RULES, self::plugin()->translate("rules", RulesGUI::LANG_MODULE_RULES), self::dic()
						->ctrl()->getLinkTargetByClass([
							ilUIPluginRouterGUI::class,
							RulesGUI::class
						], RulesGUI::CMD_LIST_RULES));
				}
			}
		}
	}
}
