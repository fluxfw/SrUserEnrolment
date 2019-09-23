<?php

namespace srag\Plugins\SrUserEnrolment\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_TARGET = "target";
    const GET_PARAM_USER_ID = "user_id";
    /**
     * @var self
     */
    protected static $instance = null;


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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param Rule $rule
     */
    public function deleteRule(Rule $rule)/*: void*/
    {
        $rule->delete();
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return int|null
     */
    public function getObjId()/*: ?int*/
    {
        $ref_id = $this->getRefId();

        if ($ref_id === null) {
            return null;
        }

        return self::dic()->objDataCache()->lookupObjId($ref_id);
    }


    /**
     * @return int|null
     */
    public function getRefId()/*: ?int*/
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }


    /**
     * @return array
     */
    public function getOperatorsAllText() : array
    {
        return array_map(function (string $operator) : string {
            return self::plugin()->translate("operator_" . $operator, RulesGUI::LANG_MODULE_RULES);
        }, Rule::$operators_title + Rule::$operators_ref_id);
    }


    /**
     * @return array
     */
    public function getOperatorsRefIdText() : array
    {
        return array_map(function (string $operator) : string {
            return self::plugin()->translate("operator_" . $operator, RulesGUI::LANG_MODULE_RULES);
        }, Rule::$operators_ref_id);
    }


    /**
     * @return array
     */
    public function getOperatorsTitleText() : array
    {
        return array_map(function (string $operator) : string {
            return self::plugin()->translate("operator_" . $operator, RulesGUI::LANG_MODULE_RULES);
        }, Rule::$operators_title);
    }


    /**
     * @param int $rule_id
     *
     * @return Rule|null
     */
    public function getRuleById(int $rule_id)/*: ?Rule*/
    {
        /**
         * @var Rule|null $rule
         */

        $rule = Rule::where(["rule_id" => $rule_id])->first();

        return $rule;
    }


    /**
     * @param int|null $object_id
     * @param bool     $only_enabled
     *
     * @return Rule[]
     */
    public function getRules(/*?*/ int $object_id = null, bool $only_enabled = true) : array
    {
        $where = Rule::where([]);

        if ($object_id !== null) {
            $where = $where->where(["object_id" => $object_id]);
        }

        if ($only_enabled) {
            $where = $where->where(["enabled" => true]);
        }

        return $where->get();
    }


    /**
     * @param int $object_id
     *
     * @return array
     */
    public function getRulesArray(int $object_id) : array
    {
        return Rule::where(["object_id" => $object_id])->getArray();
    }


    /**
     * @return int|null
     */
    public function getUserId()/*: ?int*/
    {
        $user_id = filter_input(INPUT_GET, self::GET_PARAM_USER_ID);

        $user_id = intval($user_id);

        if ($user_id > 0) {
            return $user_id;
        } else {
            return null;
        }
    }


    /**
     * @param Rule $rule
     */
    public function storeRule(Rule $rule)/*: void*/
    {
        $rule->store();
    }
}
