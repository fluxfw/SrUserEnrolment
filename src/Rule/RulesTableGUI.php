<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RulesTableGUI extends TableGUI {

	use SrUserEnrolmentTrait;
	const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
	const LANG_MODULE = RulesGUI::LANG_MODULE_RULES;


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT): string {
		switch ($column) {
			default:
				$column = $row[$column];
				break;
		}

		return strval($column);
	}


	/**
	 * @inheritdoc
	 */
	public function getSelectableColumns2(): array {
		$columns = [
			"enabled" => "enabled",
			"title" => "title",
			"description" => "description"
		];

		$columns = array_map(function (string $key): array {
			return [
				"id" => $key,
				"default" => true,
				"sort" => true
			];
		}, $columns);

		return $columns;
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn("");

		parent::initColumns();

		$this->addColumn($this->txt("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_rule"), self::dic()->ctrl()
			->getLinkTarget($this->parent_obj, RulesGUI::CMD_ADD_RULE)));

		$this->setSelectAllCheckbox("rule_id");
		$this->addMultiCommand(RulesGUI::CMD_ENABLE_RULES, $this->txt("enable_rules"));
		$this->addMultiCommand(RulesGUI::CMD_DISABLE_RULES, $this->txt("disable_rules"));
		$this->addMultiCommand(RulesGUI::CMD_REMOVE_RULES_CONFIRM, $this->txt("remove_rules"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setData(array_map(function (array &$row): array {
			if ($row["enabled"]) {
				$enabled = ilUtil::getImagePath("icon_ok.svg");
			} else {
				$enabled = ilUtil::getImagePath("icon_not_ok.svg");
			}
			$row["enabled"] = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($enabled, ""));

			return $row;
		}, self::rules()->getRulesArray(self::rules()->getRefId())));
	}
	/**
	 *
	 */

	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
		$this->filter_fields = [];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srusrenr_tickets");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle($this->txt("rules"));
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/ $row)/*: void*/ {
		self::dic()->ctrl()->setParameter($this->parent_obj, "rule_id", $row["rule_id"]);

		$this->tpl->setCurrentBlock("checkbox");
		$this->tpl->setVariable("CHECKBOX_POST_VAR", "rule_id");
		$this->tpl->setVariable("ID", $row["rule_id"]);
		$this->tpl->parseCurrentBlock();

		parent::fillRow($row);

		$this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
			self::dic()->ui()->factory()->button()->shy($this->txt("edit_rule"), self::dic()->ctrl()
				->getLinkTarget($this->parent_obj, RulesGUI::CMD_EDIT_RULE)),
			self::dic()->ui()->factory()->button()->shy($this->txt("remove_rule"), self::dic()->ctrl()
				->getLinkTarget($this->parent_obj, RulesGUI::CMD_REMOVE_RULE_CONFIRM))
		])->withLabel($this->txt("actions"))));
	}
}
