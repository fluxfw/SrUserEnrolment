<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilCheckboxInputGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
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
			"org_unit_type" => [
				self::PROPERTY_CLASS => ilRadioGroupInputGUI::class,
				self::PROPERTY_SUBITEMS => [
					Rule::ORG_UNIT_TYPE_TITLE => [
						self::PROPERTY_CLASS => ilRadioOption::class,
						self::PROPERTY_SUBITEMS => [
							"org_unit_title" => [
								self::PROPERTY_CLASS => ilTextInputGUI::class,
								"setTitle" => $this->txt("title")
							],
							"title_operator" => [
								self::PROPERTY_CLASS => ilSelectInputGUI::class,
								self::PROPERTY_REQUIRED => true,
								self::PROPERTY_OPTIONS => [ "" => "" ] + self::rules()->getOperatorsTitleText(),
								"setTitle" => $this->txt("operator")
							],
							"title_operator_negated" => [
								self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
								"setTitle" => $this->txt("operator_negated")
							],
							"title_operator_case_sensitive" => [
								self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
								"setTitle" => $this->txt("operator_case_sensitive")
							]
						],
						"setTitle" => $this->txt("org_unit_title")
					],
					Rule::ORG_UNIT_TYPE_TREE => [
						self::PROPERTY_CLASS => ilRadioOption::class,
						self::PROPERTY_SUBITEMS => [
							"org_unit_ref_id" => [
								self::PROPERTY_CLASS => ilNumberInputGUI::class,
								"setTitle" => $this->txt("ref_id")
							],
							"ref_id_operator" => [
								self::PROPERTY_CLASS => ilSelectInputGUI::class,
								self::PROPERTY_REQUIRED => true,
								self::PROPERTY_OPTIONS => [ "" => "" ] + self::rules()->getOperatorsRefIdText(),
								"setTitle" => $this->txt("operator")
							],
						],
						"setTitle" => $this->txt("org_unit_tree")
					]
				]
			],
			"position" => [
				self::PROPERTY_CLASS => ilSelectInputGUI::class,
				self::PROPERTY_REQUIRED => true,
				self::PROPERTY_OPTIONS => [ 0 => $this->txt("all") ] + self::ilias()->orgUnits()->getPositions()
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
		$this->object->setCourseRefId(self::rules()->getCourseRefId());

		return parent::storeForm();
	}
}
