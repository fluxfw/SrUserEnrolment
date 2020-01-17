<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\AbstractFactory;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\AbstractRepository;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository extends AbstractRepository
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
     * Repository constructor
     */
    private function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     *
     * @return Factory
     */
    public function factory() : AbstractFactory
    {
        return Factory::getInstance();
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return ilSrUserEnrolmentPlugin::PLUGIN_ID . "_config";
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            ConfigFormGUI::KEY_ROLES                                      => [Config::TYPE_JSON, []],
            ConfigFormGUI::KEY_SHOW_ASSISTANTS                            => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_ENROLMENT_WORKFLOW                    => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT                          => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_CONFIG                   => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_SHOW_RESET_PASSWORD                        => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_RULES_ENROLL                          => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_COUNT_SKIP_TOP_ROWS                   => [Config::TYPE_INTEGER, 0],
            ExcelImportFormGUI::KEY_CREATE_NEW_USERS                      => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_FIELDS                                => [
                Config::TYPE_JSON,
                [
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "login",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "email",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "firstname",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "lastname",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "passwd",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "gender",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "time_limit_owner",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "org_unit",
                        "column_heading" => ""
                    ],
                    [
                        "type"           => ExcelImport::FIELDS_TYPE_ILIAS,
                        "key"            => "org_unit_position",
                        "column_heading" => ""
                    ]
                ],
                true
            ],
            ExcelImportFormGUI::KEY_GENDER_F                              => [Config::TYPE_STRING, "f"],
            ExcelImportFormGUI::KEY_GENDER_M                              => [Config::TYPE_STRING, "m"],
            ExcelImportFormGUI::KEY_GENDER_N                              => [Config::TYPE_STRING, "n"],
            ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION             => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE => [
                Config::TYPE_INTEGER,
                ExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY
            ],
            ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION_TYPE        => [Config::TYPE_INTEGER, ExcelImport::LOCAL_USER_ADMINISTRATION_TYPE_TITLE],
            ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD                => [Config::TYPE_INTEGER, ExcelImport::MAP_EXISTS_USERS_LOGIN],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN                       => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN_POSITION              => [Config::TYPE_INTEGER, ExcelImport::ORG_UNIT_POSITION_FIELD],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN_TYPE                  => [Config::TYPE_INTEGER, ExcelImport::ORG_UNIT_TYPE_TITLE],
            ExcelImportFormGUI::KEY_SET_PASSWORD                          => [Config::TYPE_INTEGER, ExcelImport::SET_PASSWORD_RANDOM],
            ExcelImportFormGUI::KEY_SET_PASSWORD_FORMAT_DATE              => [Config::TYPE_BOOLEAN, false]
        ];
    }
}
