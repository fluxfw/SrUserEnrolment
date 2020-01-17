<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilSrUserEnrolmentConfigGUI;
use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\AbstractFactory;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory extends AbstractFactory
{

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
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @param ilSrUserEnrolmentConfigGUI $parent
     *
     * @return ConfigFormGUI
     */
    public function newFormInstance(ilSrUserEnrolmentConfigGUI $parent) : ConfigFormGUI
    {
        $form = new ConfigFormGUI($parent);

        return $form;
    }
}
