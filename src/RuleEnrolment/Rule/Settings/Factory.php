<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\Form\FormBuilder;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings
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
     * @param RulesCourseSettingsGUI $parent
     * @param Settings               $settings
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(RulesCourseSettingsGUI $parent, Settings $settings) : FormBuilder
    {
        $form = new FormBuilder($parent, $settings);

        return $form;
    }


    /**
     * @return Settings
     */
    public function newInstance() : Settings
    {
        $settings = new Settings();

        return $settings;
    }
}
