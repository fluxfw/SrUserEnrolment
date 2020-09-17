<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleChecker;
use stdClass;

/**
 * Class LanguageChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LanguageChecker extends AbstractRuleChecker
{

    /**
     * @var Language
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(Language $rule)
    {
        parent::__construct($rule);
    }


    /**
     * @inheritDoc
     */
    public function check(int $user_id, int $obj_ref_id) : bool
    {
        return in_array(self::srUserEnrolment()->getIliasObjectById($user_id)->getLanguage(), $this->rule->getLanguages());
    }


    /**
     * @inheritDoc
     */
    protected function getObjectsUsers() : array
    {
        return array_map(function (int $user_id) : stdClass {
            return (object) [
                "obj_ref_id" => $this->rule->getParentId(),
                "user_id"    => $user_id
            ];
        }, array_keys(self::srUserEnrolment()->ruleEnrolment()->getUsers()));
    }
}
