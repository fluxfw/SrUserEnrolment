<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Storage;

use srag\DataTableUI\SrUserEnrolment\Component\Settings\Storage\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Storage\SettingsStorage;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Storage
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableUITrait;

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
     * @inheritDoc
     */
    public function default() : SettingsStorage
    {
        return new DefaultSettingsStorage();
    }
}
