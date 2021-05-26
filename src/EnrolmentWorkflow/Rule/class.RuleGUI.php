<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\GroupRulesGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RuleGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI
 */
class RuleGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_ADD_RULE = "addRule";
    const CMD_BACK = "back";
    const CMD_CREATE_RULE = "createRule";
    const CMD_EDIT_RULE = "editRule";
    const CMD_MOVE_RULE_DOWN = "moveRuleDown";
    const CMD_MOVE_RULE_UP = "moveRuleUp";
    const CMD_REMOVE_RULE = "removeRule";
    const CMD_REMOVE_RULE_CONFIRM = "removeRuleConfirm";
    const CMD_UNGROUP = "ungroup";
    const CMD_UPDATE_RULE = "updateRule";
    const GET_PARAM_RULE_ID = "rule_id_";
    const GET_PARAM_RULE_TYPE = "rule_type_";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_EDIT_RULE = "edit_rule";
    /**
     * @var RulesGUI
     */
    protected $parent;
    /**
     * @var AbstractRule|null
     */
    protected $rule = null;


    /**
     * RuleGUI constructor
     *
     * @param RulesGUI $parent
     */
    public function __construct(RulesGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->rule = self::srUserEnrolment()->enrolmentWorkflow()
            ->rules()
            ->getRuleById($this->parent->getParentContext(), $this->parent->getParentId(), strval(filter_input(INPUT_GET, self::GET_PARAM_RULE_TYPE . $this->parent->getParentContext())),
                intval(filter_input(INPUT_GET, self::GET_PARAM_RULE_ID . $this->parent->getParentContext())));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_RULE_TYPE . $this->parent->getParentContext());
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_RULE_ID . $this->parent->getParentContext());

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(GroupRulesGUI::class):
                self::dic()->ctrl()->forwardCommand(new GroupRulesGUI(AbstractRule::PARENT_CONTEXT_RULE_GROUP, $this->rule->getRuleId()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_RULE:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_RULE:
                    case self::CMD_EDIT_RULE:
                    case self::CMD_MOVE_RULE_DOWN:
                    case self::CMD_MOVE_RULE_UP:
                    case self::CMD_REMOVE_RULE:
                    case self::CMD_REMOVE_RULE_CONFIRM:
                    case self::CMD_UPDATE_RULE:
                    case self::CMD_UNGROUP:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return RulesGUI
     */
    public function getParent() : RulesGUI
    {
        return $this->parent;
    }


    /**
     *
     */
    protected function addRule()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newCreateFormInstance($this);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirect($this->parent, RulesGUI::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function createRule()/*: void*/
    {
        $form = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newCreateFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        $this->rule = $form->getRule();

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_RULE_TYPE . $this->parent->getParentContext(), $this->rule->getRuleType());
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_RULE_ID . $this->parent->getParentContext(), $this->rule->getRuleId());

        ilUtil::sendSuccess(self::plugin()->translate("added_rule", RulesGUI::LANG_MODULE, [$this->rule->getRuleTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_RULE);
    }


    /**
     *
     */
    protected function editRule()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_RULE);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newFormInstance($this, $this->rule);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function moveRuleDown()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->rules()->moveRuleDown($this->rule);

        exit;
    }


    /**
     *
     */
    protected function moveRuleUp()
    {
        self::srUserEnrolment()->enrolmentWorkflow()->rules()->moveRuleUp($this->rule);

        exit;
    }


    /**
     *
     */
    protected function removeRule()/*: void*/
    {
        self::srUserEnrolment()->enrolmentWorkflow()->rules()->deleteRule($this->rule);

        ilUtil::sendSuccess(self::plugin()->translate("removed_rule", RulesGUI::LANG_MODULE, [$this->rule->getRuleTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeRuleConfirm()/*: void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()
            ->translate("remove_rule_confirm", RulesGUI::LANG_MODULE, [$this->rule->getRuleTitle()]));

        $confirmation->addItem(self::GET_PARAM_RULE_ID . $this->parent->getParentContext(), $this->rule->getId(), $this->rule->getRuleTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", RulesGUI::LANG_MODULE), self::CMD_REMOVE_RULE);
        $confirmation->setCancel(self::plugin()->translate("cancel", RulesGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("type_" . AbstractRule::TYPES[$this->parent->getParentContext()][$this->parent->getType()], RulesGUI::LANG_MODULE),
            self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK));

        if ($this->rule !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_RULE_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_RULE, self::plugin()->translate("remove_rule", RulesGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_RULE_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_RULE, self::plugin()->translate("edit_rule", RulesGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_RULE));

                self::dic()->locator()->addItem($this->rule->getRuleTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_RULE));
            }
        } else {
            self::dic()->tabs()->addTab(self::TAB_EDIT_RULE, self::plugin()->translate("add_rule", RulesGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_RULE));
        }
    }


    /**
     *
     */
    protected function ungroup()/*:void*/
    {
        self::srUserEnrolment()->enrolmentWorkflow()->rules()->ungroup($this->rule);

        ilUtil::sendSuccess(self::plugin()->translate("removed_rule", RulesGUI::LANG_MODULE, [$this->rule->getRuleTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function updateRule()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_RULE);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newFormInstance($this, $this->rule);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_rule", RulesGUI::LANG_MODULE, [$this->rule->getRuleTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_RULE);
    }
}
