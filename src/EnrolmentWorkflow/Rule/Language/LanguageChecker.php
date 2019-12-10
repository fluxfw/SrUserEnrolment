<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language;

use ilMD;
use ilObjCourse;
use ilObjectFactory;
use ilObjUser;
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
        $obj = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);

        if ($obj instanceof ilObjCourse) {
            $obj_md = new ilMD($obj->getId(), $obj->getId(), $obj->getType());

            $obj_lang = $obj_md->getGeneral()->getLanguage(current($obj_md->getGeneral()->getLanguageIds()))->getLanguageCode();

            return (strval($obj_lang) === strval(((new ilObjUser($user_id))->getLanguage())));
        }

        return false;
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
