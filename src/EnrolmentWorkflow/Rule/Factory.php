<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Always\Always;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\CurrentUserIsAssignedAsResponsibleUser\CurrentUserIsAssignedAsResponsibleUser;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\GlobalRole\GlobalRole;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group\Group;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission\IliasPermission;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language\Language;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoMembership\NoMembership;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoOtherRequests\NoOtherRequests;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\NoResponsibleUsersAssigned\NoResponsibleUsersAssigned;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitSuperior\OrgUnitSuperior;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType\OrgUnitUserType;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests\TotalRequests;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDF;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDFSupervisor\UDFSupervisor;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var array
     */
    protected $classes
        = [
            Always::class,
            CurrentUserIsAssignedAsResponsibleUser::class,
            GlobalRole::class,
            Group::class,
            IliasPermission::class,
            Language::class,
            NoMembership::class,
            NoOtherRequests::class,
            NoResponsibleUsersAssigned::class,
            OrgUnitSuperior::class,
            OrgUnitUserType::class,
            TotalRequests::class,
            UDF::class,
            UDFSupervisor::class
        ];


    /**
     * Factory constructor
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
     * @param string $class
     */
    public function addClass(string $class)/*:void*/
    {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }
    }


    /**
     * @param int|null $parent_context
     *
     * @return string[]
     */
    public function getRuleTypes(/*?*/ int $parent_context = null) : array
    {
        $rule_types = array_filter($this->classes, function (string $class) use ($parent_context) : bool {
            return $class::supportsParentContext($parent_context);
        });

        $rule_types = array_combine(array_map(function (string $class) : string {
            return $class::getRuleType();
        }, $rule_types), $rule_types);

        ksort($rule_types);

        return $rule_types;
    }


    /**
     * @param AbstractRule $rule
     *
     * @return AbstractRuleChecker
     */
    public function newCheckerInstance(AbstractRule $rule) : AbstractRuleChecker
    {
        $class = get_class($rule) . "Checker";

        $checker = new $class($rule);

        return $checker;
    }


    /**
     * @param RuleGUI $parent
     *
     * @return CreateRuleFormGUI
     */
    public function newCreateFormInstance(RuleGUI $parent) : CreateRuleFormGUI
    {
        $form = new CreateRuleFormGUI($parent);

        return $form;
    }


    /**
     * @param RuleGUI      $parent
     * @param AbstractRule $rule
     *
     * @return AbstractRuleFormGUI
     */
    public function newFormInstance(RuleGUI $parent, AbstractRule $rule) : AbstractRuleFormGUI
    {
        $class = get_class($rule) . "FormGUI";

        $form = new $class($parent, $rule);

        return $form;
    }


    /**
     * @param string $rule_type
     *
     * @return AbstractRule|null
     */
    public function newInstance(string $rule_type) /*: ?AbstractRule*/
    {
        $rule = null;

        foreach ($this->getRuleTypes() as $rule_type_class => $class) {
            if ($rule_type_class === $rule_type) {
                $rule = new $class();
                break;
            }
        }

        return $rule;
    }


    /**
     * @param RulesGUI $parent
     * @param string   $cmd
     *
     * @return RulesTableGUI
     */
    public function newTableInstance(RulesGUI $parent, string $cmd = RulesGUI::CMD_LIST_RULES) : RulesTableGUI
    {
        $table = new RulesTableGUI($parent, $cmd);

        return $table;
    }
}
