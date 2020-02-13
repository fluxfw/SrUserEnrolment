<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractActionRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractActionRunner
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var AbstractAction
     */
    protected $action;


    /**
     * AbstractActionRunner constructor
     *
     * @param AbstractAction $action
     */
    public function __construct(AbstractAction $action)
    {
        $this->action = $action;
    }


    /**
     * @param Request $request
     */
    public abstract function run(Request $request)/*:void*/ ;
}
