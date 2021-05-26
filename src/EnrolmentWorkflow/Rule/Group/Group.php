<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class Group
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Group
 */
class Group extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "group";


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_RULE_GROUP:
                return false;

            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        $descriptions = array_map(function (AbstractRule $rule) : string {
            return $rule->getRuleTitle();
        }, self::srUserEnrolment()->enrolmentWorkflow()
            ->rules()
            ->getRules(self::PARENT_CONTEXT_RULE_GROUP, self::TYPE_RULE_GROUP, $this->rule_id));

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
    }
}
