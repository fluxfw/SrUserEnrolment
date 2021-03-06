<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
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
     * @param WorkflowGUI $parent
     * @param Workflow    $workflow
     *
     * @return WorkflowFormGUI
     */
    public function newFormInstance(WorkflowGUI $parent, Workflow $workflow) : WorkflowFormGUI
    {
        $form = new WorkflowFormGUI($parent, $workflow);

        return $form;
    }


    /**
     * @return Workflow
     */
    public function newInstance() : Workflow
    {
        $workflow = new Workflow();

        return $workflow;
    }


    /**
     * @param WorkflowsGUI $parent
     * @param string       $cmd
     *
     * @return WorkflowsTableGUI
     */
    public function newTableInstance(WorkflowsGUI $parent, string $cmd = WorkflowsGUI::CMD_LIST_WORKFLOWS) : WorkflowsTableGUI
    {
        $table = new WorkflowsTableGUI($parent, $cmd);

        return $table;
    }
}
