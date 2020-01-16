<?php

namespace srag\RequiredData\SrUserEnrolment;

use LogicException;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Plugin\Pluginable;
use srag\DIC\SrUserEnrolment\Plugin\PluginInterface;
use srag\DIC\SrUserEnrolment\Util\LibraryLanguageInstaller;
use srag\RequiredData\SrUserEnrolment\Field\Repository as FieldsRepository;
use srag\RequiredData\SrUserEnrolment\Fill\Repository as FillsRepository;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class Repository
 *
 * @package srag\RequiredData\SrUserEnrolment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository implements Pluginable
{

    use DICTrait;
    use RequiredDataTrait;
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
     * @var string
     */
    protected $table_name_prefix = "";
    /**
     * @var PluginInterface
     */
    protected $plugin;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    public function dropTables()/*:void*/
    {
        $this->fields()->dropTables();
        $this->fills()->dropTables();
    }


    /**
     * @return FieldsRepository
     */
    public function fields() : FieldsRepository
    {
        return FieldsRepository::getInstance();
    }


    /**
     * @return FillsRepository
     */
    public function fills() : FillsRepository
    {
        return FillsRepository::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function getPlugin() : PluginInterface
    {
        if (empty($this->plugin)) {
            throw new LogicException("plugin is empty - please call withPlugin earlier!");
        }

        return $this->plugin;
    }


    /**
     * @return string
     */
    public function getTableNamePrefix() : string
    {
        if (empty($this->table_name_prefix)) {
            throw new LogicException("table name prefix is empty - please call withTableNamePrefix earlier!");
        }

        return $this->table_name_prefix;
    }


    /**
     *
     */
    public function installLanguages()/*:void*/
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($this->getPlugin())->withLibraryLanguageDirectory(__DIR__
            . "/../lang")->updateLanguages();
    }


    /**
     *
     */
    public function installTables()/*:void*/
    {
        $this->fields()->installTables();
        $this->fills()->installTables();
    }


    /**
     * @inheritDoc
     */
    public function withPlugin(PluginInterface $plugin) : self
    {
        $this->plugin = $plugin;

        return $this;
    }


    /**
     * @param string $table_name_prefix
     *
     * @return self
     */
    public function withTableNamePrefix(string $table_name_prefix) : self
    {
        $this->table_name_prefix = $table_name_prefix;

        return $this;
    }
}
