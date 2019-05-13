<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\Rule
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\Rule\RulesGUI: ilUIPluginRouterGUI
 */
class RulesGUI {

	use DICTrait;
	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const CMD_LIST_RULES = "listRules";
	const TAB_RULES = "rules";
	const LANG_MODULE_RULES = "rules";


	/**
	 * RulesGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->currentUserHasRole()) {
			die();
		}

		$this->setTabs();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_LIST_RULES:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("crs");

		self::dic()->ctrl()->saveParameter($this, Rules::GET_PARAM_REF_ID);
		self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, Rules::GET_PARAM_REF_ID);

		self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", self::LANG_MODULE_RULES), self::dic()->ctrl()->getLinkTargetByClass([
			ilRepositoryGUI::class,
			ilObjCourseGUI::class,
			ilCourseMembershipGUI::class
		]));

		self::dic()->tabs()->addTab(RulesGUI::TAB_RULES, self::plugin()->translate("rules", self::LANG_MODULE_RULES), self::dic()->ctrl()
			->getLinkTarget($this, RulesGUI::CMD_LIST_RULES));
		self::dic()->tabs()->activateTab(RulesGUI::TAB_RULES);
	}


	/**
	 * @param string $cmd
	 *
	 * @return RulesTableGUI
	 */
	protected function getRulesTable(string $cmd = self::CMD_LIST_RULES): RulesTableGUI {
		$table = new RulesTableGUI($this, $cmd);

		return $table;
	}


	/**
	 *
	 */
	protected function listRules()/*: void*/ {
		$table = $this->getRulesTable();

		self::output()->output($table, true);
	}
}
