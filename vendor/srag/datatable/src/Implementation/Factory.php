<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation;

use srag\DataTableUI\SrUserEnrolment\Component\Column\Factory as ColumnFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Data\Factory as DataFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Data\Fetcher\DataFetcher;
use srag\DataTableUI\SrUserEnrolment\Component\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Format\Factory as FormatFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Factory as SettingsFactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Table as TableInterface;
use srag\DataTableUI\SrUserEnrolment\Implementation\Column\Factory as ColumnFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Data\Factory as DataFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Format\Factory as FormatFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Factory as SettingsFactory;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Plugin\PluginInterface;
use srag\LibraryLanguageInstaller\SrUserEnrolment\LibraryLanguageInstaller;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation
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
    public function column() : ColumnFactoryInterface
    {
        return ColumnFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function data() : DataFactoryInterface
    {
        return DataFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function format() : FormatFactoryInterface
    {
        return FormatFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function installLanguages(PluginInterface $plugin)/* : void*/
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($plugin)->withLibraryLanguageDirectory(__DIR__
            . "/../../lang")->updateLanguages();
    }


    /**
     * @inheritDoc
     */
    public function settings() : SettingsFactoryInterface
    {
        return SettingsFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function table(string $table_id, string $action_url, string $title, array $columns, DataFetcher $data_fetcher) : TableInterface
    {
        return new Table($table_id, $action_url, $title, $columns, $data_fetcher);
    }
}
