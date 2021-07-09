<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilConfirmationGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User\RulesUserGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI
 */
class RulesGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_CREATE_GROUP_OF_RULES = "createGroupOfRules";
    const CMD_DISABLE_RULES = "disableRules";
    const CMD_ENABLE_RULES = "enableRules";
    const CMD_LIST_RULES = "listRules";
    const CMD_REMOVE_RULES = "removeRules";
    const CMD_REMOVE_RULES_CONFIRM = "removeRulesConfirm";
    const GET_PARAM_TYPE = "type_";
    const LANG_MODULE = "rules";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_LIST_RULES = "list_rules_";
    /**
     * @var int
     */
    protected $parent_context;
    /**
     * @var string
     */
    protected $parent_id;
    /**
     * @var int
     */
    protected $type;


    /**
     * RulesGUI constructor
     *
     * @param int    $parent_context
     * @param string $parent_id
     */
    public function __construct(int $parent_context, string $parent_id)
    {
        $this->parent_context = $parent_context;
        $this->parent_id = $parent_id;
    }


    /**
     * @param int $parent_context
     */
    public static function addTabs(int $parent_context) : void
    {
        foreach (AbstractRule::TYPES[$parent_context] as $type => $type_lang_key) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_TYPE . $parent_context, $type);
            self::dic()->tabs()->addTab(self::TAB_LIST_RULES . $parent_context . "_" . $type,
                self::plugin()->translate("type_" . $type_lang_key, self::LANG_MODULE),
                self::dic()->ctrl()
                    ->getLinkTargetByClass(static::class, self::CMD_LIST_RULES));
        }
        self::dic()
            ->ctrl()
            ->setParameterByClass(self::class, self::GET_PARAM_TYPE . $parent_context, filter_input(INPUT_GET, self::GET_PARAM_TYPE . $parent_context));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_TYPE . $this->parent_context);
        $this->type = intval(filter_input(INPUT_GET, self::GET_PARAM_TYPE . $this->parent_context));

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower($this->getRuleGUIClass()):
                $class = $this->getRuleGUIClass();
                self::dic()->ctrl()->forwardCommand(new $class($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CREATE_GROUP_OF_RULES:
                    case self::CMD_DISABLE_RULES:
                    case self::CMD_ENABLE_RULES:
                    case self::CMD_LIST_RULES:
                    case self::CMD_REMOVE_RULES:
                    case self::CMD_REMOVE_RULES_CONFIRM:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return int
     */
    public function getParentContext() : int
    {
        return $this->parent_context;
    }


    /**
     * @return string
     */
    public function getParentId() : string
    {
        return $this->parent_id;
    }


    /**
     * @return string
     */
    public function getRuleGUIClass() : string
    {
        return RuleGUI::class;
    }


    /**
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }


    /**
     *
     */
    protected function createGroupOfRules() : void
    {
        $rule_ids = filter_input(INPUT_POST, RuleGUI::GET_PARAM_RULE_ID . $this->parent_context, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($rule_ids)) {
            $rule_ids = [];
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_map(function (string $rule_id) : AbstractRule {
            list($rule_type, $rule_id) = explode("_", $rule_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRuleById($this->parent_context, $this->parent_id, $rule_type, $rule_id);
        }, $rule_ids);

        $group = self::srUserEnrolment()->enrolmentWorkflow()->rules()->createGroupOfRules($rules);

        ilUtil::sendSuccess(self::plugin()->translate("saved_rule", self::LANG_MODULE, [$group->getRuleTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function disableRules() : void
    {
        $rule_ids = filter_input(INPUT_POST, RuleGUI::GET_PARAM_RULE_ID . $this->parent_context, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($rule_ids)) {
            $rule_ids = [];
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_map(function (string $rule_id) : AbstractRule {
            list($rule_type, $rule_id) = explode("_", $rule_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRuleById($this->parent_context, $this->parent_id, $rule_type, $rule_id);
        }, $rule_ids);

        foreach ($rules as $rule) {
            $rule->setEnabled(false);

            $rule->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("disabled_rules", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function enableRules() : void
    {
        $rule_ids = filter_input(INPUT_POST, RuleGUI::GET_PARAM_RULE_ID . $this->parent_context, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($rule_ids)) {
            $rule_ids = [];
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_map(function (string $rule_id) : AbstractRule {
            list($rule_type, $rule_id) = explode("_", $rule_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRuleById($this->parent_context, $this->parent_id, $rule_type, $rule_id);
        }, $rule_ids);

        foreach ($rules as $rule) {
            $rule->setEnabled(true);

            $rule->store();
        }

        ilUtil::sendSuccess(self::plugin()->translate("enabled_rules", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function listRules() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_RULES . $this->parent_context . "_" . $this->type);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newTableInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function removeRules() : void
    {
        $rule_ids = filter_input(INPUT_POST, RuleGUI::GET_PARAM_RULE_ID . $this->parent_context, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($rule_ids)) {
            $rule_ids = [];
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_map(function (string $rule_id) : AbstractRule {
            list($rule_type, $rule_id) = explode("_", $rule_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRuleById($this->parent_context, $this->parent_id, $rule_type, $rule_id);
        }, $rule_ids);

        foreach ($rules as $rule) {
            self::srUserEnrolment()->enrolmentWorkflow()->rules()->deleteRule($rule);
        }

        ilUtil::sendSuccess(self::plugin()->translate("removed_rules", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_LIST_RULES);
    }


    /**
     *
     */
    protected function removeRulesConfirm() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_RULES . $this->parent_context . "_" . $this->type);

        $rule_ids = filter_input(INPUT_POST, RuleGUI::GET_PARAM_RULE_ID . $this->parent_context, FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (!is_array($rule_ids)) {
            $rule_ids = [];
        }

        /**
         * @var AbstractRule[] $rules
         */
        $rules = array_map(function (string $rule_id) : AbstractRule {
            list($rule_type, $rule_id) = explode("_", $rule_id);

            return self::srUserEnrolment()->enrolmentWorkflow()->rules()->getRuleById($this->parent_context, $this->parent_id, $rule_type, $rule_id);
        }, $rule_ids);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_rules_confirm", self::LANG_MODULE));

        foreach ($rules as $rule) {
            $confirmation->addItem(RuleGUI::GET_PARAM_RULE_ID . $this->parent_context . "[]", $rule->getId(), $rule->getRuleTitle());
        }

        $confirmation->setConfirm(self::plugin()->translate("remove", self::LANG_MODULE), self::CMD_REMOVE_RULES);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_LIST_RULES);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }
}
