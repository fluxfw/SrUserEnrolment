<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class AbstractRuleChecker
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule
 */
abstract class AbstractRuleChecker
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var Request|null $request
     */
    protected $request = null;
    /**
     * @var AbstractRule
     */
    protected $rule;


    /**
     * AbstractRuleChecker constructor
     *
     * @param AbstractRule $rule
     */
    public function __construct(AbstractRule $rule)
    {
        $this->rule = $rule;
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public abstract function check(int $user_id, int $obj_ref_id) : bool;


    /**
     * @return stdClass[]
     */
    public function getCheckedObjectsUsers() : array
    {
        return array_filter($this->getObjectsUsers(), function (stdClass $object_user) : bool {
            return $this->check($object_user->user_id, $object_user->obj_ref_id);
        });
    }


    /**
     * @param Request|null $request
     *
     * @return self
     */
    public function withRequest(/*?*/ Request $request = null) : self
    {
        $this->request = $request;

        return $this;
    }


    /**
     * @return stdClass[]
     */
    protected abstract function getObjectsUsers() : array;
}
