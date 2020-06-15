<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy
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
     * @return CheckInactiveDeputiesJob
     */
    public function newCheckInactiveDeputiesJobInstance() : CheckInactiveDeputiesJob
    {
        $job = new CheckInactiveDeputiesJob();

        return $job;
    }


    /**
     * @param DeputiesGUI $parent
     * @param array       $deputies
     *
     * @return DeputiesFormGUI
     */
    public function newFormInstance(DeputiesGUI $parent, array $deputies) : DeputiesFormGUI
    {
        $form = new DeputiesFormGUI($parent, $deputies);

        return $form;
    }


    /**
     * @return Deputy
     */
    public function newInstance() : Deputy
    {
        $deputy = new Deputy();

        return $deputy;
    }
}
