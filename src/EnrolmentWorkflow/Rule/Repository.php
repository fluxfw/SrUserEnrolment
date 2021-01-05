<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\Group;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDF;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param AbstractRule[] $rules
     *
     * @return Group|null
     */
    public function createGroupOfRules(array $rules)/* : ?Group*/
    {
        $rules = array_filter($rules, function (AbstractRule $rule) : bool {
            return !($rule instanceof Group);
        });
        if (empty($rules)) {
            return null;
        }

        $first_rule = current($rules);

        $rules = array_filter($rules, function (AbstractRule $rule) use ($first_rule) : bool {
            return ($rule->getType() === $first_rule->getType() && $rule->getParentContext() === $first_rule->getParentContext() && $rule->getParentId() === $first_rule->getParentId());
        });
        if (empty($rules)) {
            return null;
        }

        /**
         * @var Group $group
         */
        $group = $this->factory()->newInstance(Group::getRuleType());

        $group->setType($first_rule->getType());
        $group->setParentContext($first_rule->getParentContext());
        $group->setParentId($first_rule->getParentId());
        $this->storeRule($group);

        foreach ($rules as $rule) {
            $rule->setType(AbstractRule::TYPE_RULE_GROUP);
            $rule->setParentContext(AbstractRule::PARENT_CONTEXT_RULE_GROUP);
            $rule->setParentId($group->getRuleId());
            $this->storeRule($rule);
        }

        $this->storeRule($group);

        return $group;
    }


    /**
     * @param AbstractRule $rule
     */
    public function deleteRule(AbstractRule $rule)/*: void*/
    {
        $rule->delete();

        $this->reSortRules($rule->getParentContext(), $rule->getType(), $rule->getParentId());

        if ($rule instanceof Group) {
            $this->deleteRules(AbstractRule::PARENT_CONTEXT_RULE_GROUP, $rule->getRuleId());
        }

        foreach (self::srUserEnrolment()->logs()->getLogs(null, null, null, null, null, null, null, null, null, null, null, $rule->getId()) as $log) {
            self::srUserEnrolment()->logs()->deleteLog($log);
        }
    }


    /**
     * @param int    $parent_context
     * @param string $parent_id
     */
    public function deleteRules(int $parent_context, string $parent_id)/*: void*/
    {
        foreach (AbstractRule::TYPES[$parent_context] as $type => $type_lang_key) {
            foreach ($this->getRules($parent_context, $type, $parent_id, null, false) as $rule) {
                $this->deleteRule($rule);
            }
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        foreach ($this->factory()->getRuleTypes() as $class) {
            self::dic()->database()->dropTable($class::getTableName(), false);
        }
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int          $parent_context
     * @param string       $parent_id
     * @param int          $type
     * @param int          $user_id
     * @param int          $obj_ref_id
     * @param bool         $and_operator
     * @param Request|null $request
     *
     * @return AbstractRule[]
     */
    public function getCheckedRules(int $parent_context, string $parent_id, int $type, int $user_id, int $obj_ref_id, bool $and_operator = false,/*?*/ Request $request = null) : array
    {
        $rules = $this->getRules($parent_context, $type, $parent_id);
        if (empty($rules)) {
            return [];
        }

        $checked_rules = array_filter($rules, function (AbstractRule $rule) use ($request, $user_id, $obj_ref_id) : bool {
            return $this->factory()->newCheckerInstance($rule)->withRequest($request)->check($user_id, $obj_ref_id);
        });
        if (empty($checked_rules)) {
            return [];
        }

        if ($and_operator) {
            if (count($rules) === count($checked_rules)) {
                return $rules;
            } else {
                return [];
            }
        } else {
            return $checked_rules;
        }
    }


    /**
     * @param int    $parent_context
     * @param string $parent_id
     * @param string $rule_type
     * @param int    $rule_id
     *
     * @return AbstractRule|null
     */
    public function getRuleById(int $parent_context, string $parent_id, string $rule_type, int $rule_id)/*: ?AbstractRule*/
    {
        foreach ($this->factory()->getRuleTypes() as $rule_type_class => $class) {
            if ($rule_type_class === $rule_type) {
                /**
                 * @var AbstractRule|null $rule
                 */
                $rule = $class::where(["parent_context" => $parent_context, "parent_id" => $parent_id, "rule_id" => $rule_id])->first();

                return $rule;
            }
        }

        return null;
    }


    /**
     * @param int|null    $parent_context
     * @param int|null    $type
     * @param string|null $parent_id
     * @param array|null  $types
     * @param bool        $only_enabled
     *
     * @return AbstractRule[]
     */
    public function getRules(/*?*/ int $parent_context = null, /*?*/ int $type = null, /*?*/ string $parent_id = null,/*?array*/ $types = null, bool $only_enabled = true) : array
    {
        $rules = [];

        foreach ($this->factory()->getRuleTypes($parent_context) as $class_type => $class) {
            if (!empty($types) && !in_array($class_type, $types)) {
                continue;
            }

            $where = $class::where([]);

            if (!empty($parent_context)) {
                $where = $where->where(["parent_context" => $parent_context]);
            }

            if (!empty($type)) {
                $where = $where->where(["type" => $type]);
            }

            if (!empty($parent_id)) {
                $where = $where->where(["parent_id" => $parent_id]);
            }

            if ($only_enabled) {
                $where = $where->where(["enabled" => true]);
            }

            if (!empty($parent_context) && !empty($type) && !empty($parent_id)) {
                $where = $where->orderBy("sort", "asc");
            }

            /**
             * @var AbstractRule $rule
             */
            foreach ($where->get() as $rule) {
                $rules[$rule->getId()] = $rule;
            }
        }

        return $rules;
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        $upgrade_sort_rules = false;

        foreach ($this->factory()->getRuleTypes() as $class) {
            if (!$upgrade_sort_rules) {
                $upgrade_sort_rules = (self::dic()->database()->tableExists($class::getTableName()) && !self::dic()->database()->tableColumnExists($class::getTableName(), "sort"));
            }

            $class::updateDB();
        }

        if ($upgrade_sort_rules) {
            foreach (
                array_reduce($this->getRules(null, null, null, null, false), function (array $rules, AbstractRule $rule) : array {
                    $rules[$rule->getParentContext() . "_" . $rule->getType() . "_" . $rule->getParentId()] = $rule;

                    return $rules;
                }, []) as $rule
            ) {
                $this->reSortRules($rule->getParentContext(), $rule->getType(), $rule->getParentId());
            }
        }

        if (self::dic()->database()->tableColumnExists(UDF::getTableName(), "value")) {
            foreach ($this->getRules(null, null, null, [UDF::getRuleType()], false) as $rule) {
                $rule->setValues([strval($rule->value)]);

                $this->storeRule($rule);
            }

            self::dic()->database()->dropTableColumn(UDF::getTableName(), "value");
        }
    }


    /**
     * @param AbstractRule $rule
     */
    public function moveRuleDown(AbstractRule $rule)/*: void*/
    {
        $rule->setSort($rule->getSort() + 15);

        $this->storeRule($rule);

        $this->reSortRules($rule->getParentContext(), $rule->getType(), $rule->getParentId());
    }


    /**
     * @param AbstractRule $rule
     */
    public function moveRuleUp(AbstractRule $rule)/*: void*/
    {
        $rule->setSort($rule->getSort() - 15);

        $this->storeRule($rule);

        $this->reSortRules($rule->getParentContext(), $rule->getType(), $rule->getParentId());
    }


    /**
     * @param AbstractRule $rule
     */
    public function storeRule(AbstractRule $rule)/*: void*/
    {
        if (empty($rule->getRuleId())) {
            $rule->setSort(((count($this->getRules($rule->getParentContext(), $rule->getType(), $rule->getParentId(), null, false)) + 1) * 10));
        }

        $rule->store();
    }


    /**
     * @param Group $group
     *
     * @return AbstractRule[]
     */
    public function ungroup(Group $group) : array
    {
        $rules = $this->getRules(AbstractRule::PARENT_CONTEXT_RULE_GROUP, AbstractRule::TYPE_RULE_GROUP, $group->getRuleId(), null, false);

        foreach ($rules as $rule) {
            $rule->setType($group->getType());
            $rule->setParentContext($group->getParentContext());
            $rule->setParentId($group->getParentId());
            $this->storeRule($rule);
        }

        $this->deleteRule($group);

        return $rules;
    }


    /**
     * @param int    $parent_context
     * @param int    $type
     * @param string $parent_id
     */
    protected function reSortRules(int $parent_context, int $type, string $parent_id)/*: void*/
    {
        $rules = $this->getRules($parent_context, $type, $parent_id, null, false);

        $i = 1;
        foreach ($rules as $rule) {
            $rule->setSort($i * 10);

            $this->storeRule($rule);

            $i++;
        }
    }
}
