<?php

namespace srag\RequiredData\SrUserEnrolment;

use LogicException;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Plugin\Pluginable;
use srag\DIC\SrUserEnrolment\Plugin\PluginInterface;
use srag\LibraryLanguageInstaller\SrUserEnrolment\LibraryLanguageInstaller;
use srag\RequiredData\SrUserEnrolment\Field\Repository as FieldsRepository;
use srag\RequiredData\SrUserEnrolment\Fill\Repository as FillsRepository;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class Repository
 *
 * @package srag\RequiredData\SrUserEnrolment
 */
final class Repository implements Pluginable
{

    use DICTrait;
    use RequiredDataTrait;
    use DataTableUITrait;

    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var bool
     */
    protected $enableGroups = false;
    /**
     * @var bool
     */
    protected $enableNames = false;
    /**
     * @var PluginInterface
     */
    protected $plugin;
    /**
     * @var string
     */
    protected $table_name_prefix = "";


    /**
     * Repository constructor
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
     *
     */
    public function dropTables() : void
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
    public function installLanguages() : void
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($this->getPlugin())->withLibraryLanguageDirectory(__DIR__
            . "/../lang")->updateLanguages();

        self::dataTableUI()->installLanguages($this->plugin);
    }


    /**
     *
     */
    public function installTables() : void
    {
        $this->fields()->installTables();
        $this->fills()->installTables();
    }


    /**
     * @return bool
     */
    public function isEnableGroups() : bool
    {
        return $this->enableGroups;
    }


    /**
     * @return bool
     */
    public function isEnableNames() : bool
    {
        return $this->enableNames;
    }


    /**
     * @param bool $enableGroups
     *
     * @return self
     */
    public function withEnableGroups(bool $enableGroups) : self
    {
        $this->enableGroups = $enableGroups;

        return $this;
    }


    /**
     * @param bool $enableNames
     *
     * @return self
     */
    public function withEnableNames(bool $enableNames) : self
    {
        $this->enableNames = $enableNames;

        return $this;
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
