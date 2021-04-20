<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule
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
     * @param array|null $parents
     * @param bool|null  $continue_on_crash
     * @param array|null $continue_on_crash_rules
     *
     * @return RuleEnrolmentJob
     */
    public function newJobInstance(/*?*/ array $parents = null, /*?*/ bool $continue_on_crash = null, /*?*/ array $continue_on_crash_rules = null) : RuleEnrolmentJob
    {
        $job = new RuleEnrolmentJob($parents, $continue_on_crash, $continue_on_crash_rules);

        return $job;
    }
}
