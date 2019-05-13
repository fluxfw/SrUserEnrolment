<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilCheckboxInputGUI;
use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RuleFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RuleFormGUI extends ObjectPropertyFormGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const LANG_MODULE = RulesGUI::LANG_MODULE_RULES;
	/**
	 * @var Rule|null
	 */
	protected $object;


	/**
	 * RuleFormGUI constructor
	 *
	 * @param RulesGUI $parent
	 * @param Rule     $object
	 */
	public function __construct(RulesGUI $parent, Rule $object) {
		parent::__construct($parent, $object);
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		if (!empty($this->object->getRuleId())) {
			$this->addCommandButton(RulesGUI::CMD_UPDATE_RULE, $this->txt("save"));
		} else {
			$this->addCommandButton(RulesGUI::CMD_CREATE_RULE, $this->txt("add"));
		}
		$this->addCommandButton(RulesGUI::CMD_LIST_RULES, $this->txt("cancel"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			"enabled" => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			],
			"title" => [
				self::PROPERTY_CLASS => ilTextInputGUI::class,
				self::PROPERTY_REQUIRED => true
			],
			"description" => [
				self::PROPERTY_CLASS => ilTextInputGUI::class
			],
			"operator" => [
				self::PROPERTY_CLASS => ilSelectInputGUI::class,
				self::PROPERTY_REQUIRED => true,
				self::PROPERTY_OPTIONS => [ "" => "" ] + self::rules()->getOperatorsText()
			],
			"operator_negated" => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			],
			"operator_case_sensitive" => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle($this->txt(!empty($this->object->getRuleId()) ? "edit_rule" : "add_rule"));
	}


	/**
	 * @inheritdoc
	 */
	public function storeForm()/*: bool*/ {
		$this->object->setRefId(self::rules()->getRefId());

		return parent::storeForm();
	}
}
