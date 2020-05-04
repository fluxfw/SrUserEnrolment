<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow
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
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return SelectedWorkflow
     */
    public function newInstance() : SelectedWorkflow
    {
        $selected_workflow = new SelectedWorkflow();

        return $selected_workflow;
    }


    /**
     * @return SelectWorkflowFormGUI
     */
    public function newFormInstance(SelectWorkflowGUI $parent) : SelectWorkflowFormGUI
    {
        $form = new SelectWorkflowFormGUI($parent);

        return $form;
    }
}
