<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
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
     * @param StepGUI $parent
     * @param Step    $step
     *
     * @return StepFormGUI
     */
    public function newFormInstance(StepGUI $parent, Step $step) : StepFormGUI
    {
        $form = new StepFormGUI($parent, $step);

        return $form;
    }


    /**
     * @return Step
     */
    public function newInstance() : Step
    {
        $step = new Step();

        return $step;
    }


    /**
     * @param StepsGUI $parent
     * @param string   $cmd
     *
     * @return StepsTableGUI
     */
    public function newTableInstance(StepsGUI $parent, string $cmd = StepsGUI::CMD_LIST_STEPS) : StepsTableGUI
    {
        $table = new StepsTableGUI($parent, $cmd);

        return $table;
    }
}
