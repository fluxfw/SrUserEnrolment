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
     * @var self
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
     * @return Deputy
     */
    public function newInstance() : Deputy
    {
        $deputy = new Deputy();

        return $deputy;
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
     * @param DeputiesRequestGUI $parent
     * @param string             $cmd
     *
     * @return DeputiesRequestTableGUI
     */
    public function newRequestsTableInstance(DeputiesRequestGUI $parent, string $cmd = DeputiesRequestGUI::CMD_LIST_USERS) : DeputiesRequestTableGUI
    {
        $table = new DeputiesRequestTableGUI($parent, $cmd);

        return $table;
    }
}
