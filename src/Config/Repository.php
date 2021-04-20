<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\AbstractFactory;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\AbstractRepository;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\Log\DeleteOldLogsJob;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RuleEnrolmentJob;
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
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    protected function __construct()
    {
        parent::__construct();
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
    public function getValue(string $name)
    {
        $value = parent::getValue($name);

        switch ($name) {
            case ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES:
                $value = array_filter($value, function (int $role) : bool {
                    return (!in_array($role, $this->getValue(ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES_EXCLUDE)));
                });

                return $value;

            default:
                return $value;
        }
    }


    /**
     * @inheritDoc
     */
    public function installTables()/*: void*/
    {
        parent::installTables();

        $old_map_exists_users_field = $this->getValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_DEPRECATED);
        if ($old_map_exists_users_field !== ExcelImport::MAP_EXISTS_USERS_DEPRECATED) {
            switch ($old_map_exists_users_field) {
                case ExcelImport::MAP_EXISTS_USERS_LOGIN:
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_TYPE, ExcelImport::FIELDS_TYPE_ILIAS);
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_KEY, "login");
                    break;

                case ExcelImport::MAP_EXISTS_USERS_EMAIL:
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_TYPE, ExcelImport::FIELDS_TYPE_ILIAS);
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_KEY, "email");
                    break;

                case ExcelImport::MAP_EXISTS_USERS_MATRICULATION_NUMBER:
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_TYPE, ExcelImport::FIELDS_TYPE_ILIAS);
                    $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_KEY, "matriculation");
                    break;

                case ExcelImport::MAP_EXISTS_USERS_DEPRECATED:
                default:
                    break;
            }
            $this->setValue(ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_DEPRECATED, ExcelImport::MAP_EXISTS_USERS_DEPRECATED);
        }
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            RuleEnrolmentJob::KEY_CONTINUE_ON_CRASH                       => [Config::TYPE_BOOLEAN, false],
            RuleEnrolmentJob::KEY_CONTINUE_ON_CRASH_RULES                 => [Config::TYPE_JSON, []],
            DeleteOldLogsJob::KEY_KEEP_OLD_LOGS_TIME                      => [Config::TYPE_INTEGER, 0],
            ConfigFormGUI::KEY_ROLES                                      => [Config::TYPE_JSON, []],
            ConfigFormGUI::KEY_ROLES_READ_REQUESTS                        => [Config::TYPE_JSON, []],
            ConfigFormGUI::KEY_SHOW_ASSISTANTS                            => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_ASSISTANTS_SUPERVISORS                => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_DEPUTIES                              => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_ENROLMENT_WORKFLOW                    => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT                          => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_CONFIG                   => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_COURSE                   => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER                     => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER_VIEW                => [Config::TYPE_INTEGER, ConfigFormGUI::SHOW_EXCEL_IMPORT_USER_TYPE_SEPARATE],
            ConfigFormGUI::KEY_SHOW_MEMBERS                               => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_RESET_PASSWORD                        => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_RULES_ENROLL                          => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_SHOW_RULES_ENROLL_COURSE                   => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_SHOW_RULES_ENROLL_USER                     => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_COUNT_SKIP_TOP_ROWS                   => [Config::TYPE_INTEGER, 0],
            ExcelImportFormGUI::KEY_CREATE_NEW_USERS                      => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES         => [Config::TYPE_JSON, [ExcelImportFormGUI::USER_ROLE_ID], false],
            ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES_EXCLUDE => [Config::TYPE_JSON, [SYSTEM_ROLE_ID, ANONYMOUS_ROLE_ID, ExcelImportFormGUI::GUEST_ROLE_ID], false],
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
            ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_DEPRECATED     => [Config::TYPE_INTEGER, ExcelImport::MAP_EXISTS_USERS_DEPRECATED],
            ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_GROUP          => Config::TYPE_STRING,
            ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_TYPE           => [Config::TYPE_INTEGER, ExcelImport::FIELDS_TYPE_ILIAS],
            ExcelImportFormGUI::KEY_MAP_EXISTS_USERS_FIELD_KEY            => [Config::TYPE_STRING, "login"],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN                       => [Config::TYPE_BOOLEAN, false],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN_POSITION              => [Config::TYPE_INTEGER, ExcelImport::ORG_UNIT_POSITION_FIELD],
            ExcelImportFormGUI::KEY_ORG_UNIT_ASSIGN_TYPE                  => [Config::TYPE_INTEGER, ExcelImport::ORG_UNIT_TYPE_TITLE],
            ExcelImportFormGUI::KEY_SET_PASSWORD                          => [Config::TYPE_INTEGER, ExcelImport::SET_PASSWORD_RANDOM],
            ExcelImportFormGUI::KEY_SET_PASSWORD_FORMAT_DATE              => [Config::TYPE_BOOLEAN, false]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return ilSrUserEnrolmentPlugin::PLUGIN_ID . "_config";
    }
}
