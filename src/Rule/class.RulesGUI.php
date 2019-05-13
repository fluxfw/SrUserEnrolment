<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilObject;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
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
	const CMD_ADD_RULE = "addRule";
	const CMD_CREATE_RULE = "createRule";
	const CMD_DISABLE_RULES = "disableRules";
	const CMD_EDIT_RULE = "editRule";
	const CMD_ENABLE_RULES = "enableRules";
	const CMD_LIST_RULES = "listRules";
	const CMD_REMOVE_RULE = "removeRule";
	const CMD_REMOVE_RULE_CONFIRM = "removeRuleConfirm";
	const CMD_REMOVE_RULES_CONFIRM = "removeRulesConfirm";
	const CMD_REMOVE_RULES = "removeRules";
	const CMD_UPDATE_RULE = "updateRule";
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
					case self::CMD_ADD_RULE:
					case self::CMD_CREATE_RULE:
					case self::CMD_DISABLE_RULES:
					case self::CMD_EDIT_RULE:
					case self::CMD_ENABLE_RULES:
					case self::CMD_LIST_RULES:
					case self::CMD_REMOVE_RULE:
					case self::CMD_REMOVE_RULE_CONFIRM:
					case self::CMD_REMOVE_RULES:
					case self::CMD_REMOVE_RULES_CONFIRM:
					case self::CMD_UPDATE_RULE:
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

		self::dic()->mainTemplate()->setTitleIcon(ilObject::_getIcon("", "tiny", self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()
			->lookupObjId(self::rules()->getRefId()))));

		self::dic()->mainTemplate()->setTitle(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId(self::rules()
			->getRefId())));

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


	/**
	 * @param Rule $rule
	 *
	 * @return RuleFormGUI
	 */
	public function getRuleForm(Rule $rule): RuleFormGUI {
		self::dic()->ctrl()->saveParameter($this, "rule_id");

		$form = new RuleFormGUI($this, $rule);

		return $form;
	}


	/**
	 *
	 */
	protected function addRule()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$form = $this->getRuleForm(new Rule());

		self::output()->output($form, true);
	}


	/**
	 *
	 */
	protected function createRule()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$form = $this->getRuleForm(new Rule());

		if (!$form->storeForm()) {
			self::output()->output($form, true);

			return;
		}

		ilUtil::sendSuccess(self::plugin()->translate("added_rule", self::LANG_MODULE_RULES, [ $form->getObject()->getTitle() ]), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}


	/**
	 *
	 */
	protected function editRule()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$rule_id = intval(filter_input(INPUT_GET, "rule_id"));
		$rule = self::rules()->getRuleById($rule_id);

		$form = $this->getRuleForm($rule);

		self::output()->output($form, true);
	}


	/**
	 *
	 */
	protected function updateRule()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$rule_id = intval(filter_input(INPUT_GET, "rule_id"));
		$rule = self::rules()->getRuleById($rule_id);

		$form = $this->getRuleForm($rule);

		if (!$form->storeForm()) {
			self::output()->output($form, true);

			return;
		}

		ilUtil::sendSuccess(self::plugin()->translate("saved_rule", self::LANG_MODULE_RULES, [ $rule->getTitle() ]), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}


	/**
	 *
	 */
	protected function removeRuleConfirm()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$rule_id = intval(filter_input(INPUT_GET, "rule_id"));
		$rule = self::rules()->getRuleById($rule_id);

		$confirmation = new ilConfirmationGUI();

		self::dic()->ctrl()->setParameter($this, "rule_id", $rule->getRuleId());
		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));
		self::dic()->ctrl()->setParameter($this, "rule_id", null);

		$confirmation->setHeaderText(self::plugin()->translate("remove_rule_confirm", self::LANG_MODULE_RULES, [ $rule->getTitle() ]));

		$confirmation->addItem("rule_id", $rule->getRuleId(), $rule->getTitle());

		$confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE_RULES), self::CMD_REMOVE_RULE);
		$confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_RULES), self::CMD_LIST_RULES);

		self::output()->output($confirmation, true);
	}


	/**
	 *
	 */
	protected function removeRule()/*: void*/ {
		$rule_id = intval(filter_input(INPUT_GET, "rule_id"));
		$rule = self::rules()->getRuleById($rule_id);

		$rule->delete();

		ilUtil::sendSuccess(self::plugin()->translate("removed_rule", self::LANG_MODULE_RULES, [ $rule->getTitle() ]), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}


	/**
	 *
	 */
	protected function enableRules()/*: void*/ {
		$rule_ids = filter_input(INPUT_POST, "rule_id", FILTER_DEFAULT, FILTER_FORCE_ARRAY);

		/**
		 * @var Rule[] $rules
		 */
		$rules = array_map(function (int $rule_id)/*: ?Rule*/ {
			return self::rules()->getRuleById($rule_id);
		}, $rule_ids);

		foreach ($rules as $rule) {
			$rule->setEnabled(true);

			$rule->store();
		}

		ilUtil::sendSuccess(self::plugin()->translate("enabled_rules", self::LANG_MODULE_RULES), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}


	/**
	 *
	 */
	protected function disableRules()/*: void*/ {
		$rule_ids = filter_input(INPUT_POST, "rule_id", FILTER_DEFAULT, FILTER_FORCE_ARRAY);

		/**
		 * @var Rule[] $rules
		 */
		$rules = array_map(function (int $rule_id)/*: ?Rule*/ {
			return self::rules()->getRuleById($rule_id);
		}, $rule_ids);

		foreach ($rules as $rule) {
			$rule->setEnabled(false);

			$rule->store();
		}

		ilUtil::sendSuccess(self::plugin()->translate("disabled_rules", self::LANG_MODULE_RULES), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}


	/**
	 *
	 */
	protected function removeRulesConfirm()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_RULES);

		$rule_ids = filter_input(INPUT_POST, "rule_id", FILTER_DEFAULT, FILTER_FORCE_ARRAY);

		/**
		 * @var Rule[] $rules
		 */
		$rules = array_map(function (int $rule_id)/*: ?Rule*/ {
			return self::rules()->getRuleById($rule_id);
		}, $rule_ids);

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::plugin()->translate("remove_rules_confirm", self::LANG_MODULE_RULES));

		foreach ($rules as $rule) {
			$confirmation->addItem("rule_id[]", $rule->getRuleId(), $rule->getTitle());
		}

		$confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE_RULES), self::CMD_REMOVE_RULES);
		$confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_RULES), self::CMD_LIST_RULES);

		self::output()->output($confirmation, true);
	}


	/**
	 *
	 */
	protected function removeRules()/*: void*/ {
		$rule_ids = filter_input(INPUT_POST, "rule_id", FILTER_DEFAULT, FILTER_FORCE_ARRAY);

		/**
		 * @var Rule[] $rules
		 */
		$rules = array_map(function (int $rule_id)/*: ?Rule*/ {
			return self::rules()->getRuleById($rule_id);
		}, $rule_ids);

		foreach ($rules as $rule) {
			$rule->delete();
		}

		ilUtil::sendSuccess(self::plugin()->translate("removed_rules", self::LANG_MODULE_RULES), true);

		self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
	}
}
