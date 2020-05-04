<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ilDBConstants;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType\OrgUnitUserType;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
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
     * @internal
     */
    public function dropTables()/*: void*/
    {
        self::dic()->database()->dropTable(Rule::TABLE_NAME, false);
        self::dic()->database()->dropTable(Rule::TABLE_NAME_ENROLLED, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return Rule[]
     *
     * @deprecated
     */
    protected function getRules() : array
    {
        $where = Rule::where([]);

        return $where->get();
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {
        if (self::dic()->database()->tableExists(Rule::TABLE_NAME)) {

            foreach ($this->getRules() as $rule_old) {

                /**
                 * @var OrgUnitUserType $rule
                 */
                $rule = self::srUserEnrolment()->enrolmentWorkflow()->rules()->factory()->newInstance(OrgUnitUserType::getRuleType());

                $rule->setEnabled($rule_old->isEnabled());

                $rule->setOrgUnitUserType($rule_old->getOrgUnitType());
                if ($rule_old->getOperator() > 4) {
                    $rule->setOperator($rule_old->getOperator() - 1);
                } else {
                    $rule->setOperator($rule_old->getOperator());
                }
                $rule->setOperatorCaseSensitive($rule_old->isOperatorCaseSensitive());
                $rule->setOperatorNegated($rule_old->isOperatorNegated());
                $rule->setTitle($rule_old->getTitle());
                $rule->setRefId($rule_old->getRefId());
                $rule->setPosition($rule_old->getPosition());

                $rule->setType(AbstractRule::TYPE_COURSE_RULE);
                $rule->setParentContext(AbstractRule::PARENT_CONTEXT_COURSE);
                $rule->setParentId($rule_old->getObjectId());

                self::srUserEnrolment()->enrolmentWorkflow()->rules()->storeRule($rule);

                self::dic()->database()->manipulateF("UPDATE " . self::dic()->database()->quoteIdentifier(Log::TABLE_NAME) . " SET rule_id=%s WHERE rule_id=%s",
                    [ilDBConstants::T_TEXT, ilDBConstants::T_TEXT],
                    [$rule->getId(), $rule_old->getRuleId()]);
            }
        }

        $this->dropTables();
    }
}
