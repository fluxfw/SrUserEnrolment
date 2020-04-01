<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Settings;

use ILIAS\UI\Component\ViewControl\Pagination;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Settings as SettingsInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Sort\Factory as SortFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Storage\Factory as StorageFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Sort\Factory as SortFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Storage\Factory as StorageFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Settings
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
     * @inheritDoc
     */
    public function settings(Pagination $pagination) : SettingsInterface
    {
        return new Settings($pagination);
    }


    /**
     * @inheritDoc
     */
    public function sort() : SortFactoryInterface
    {
        return SortFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function storage() : StorageFactoryInterface
    {
        return StorageFactory::getInstance();
    }
}
