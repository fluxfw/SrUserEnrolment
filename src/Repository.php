<?php

namespace srag\Plugins\SrUserEnrolment;

use ilSrUserEnrolmentPlugin;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\Config;
use srag\ActiveRecordConfig\SrUserEnrolment\Config\Repository as ConfigRepository;
use srag\ActiveRecordConfig\SrUserEnrolment\Utils\ConfigTrait;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Notifications4Plugin\SrUserEnrolment\RepositoryInterface as Notifications4PluginRepositoryInterface;
use srag\Notifications4Plugin\SrUserEnrolment\Utils\Notifications4PluginTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Repository as EnrolmentWorkflowRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect\UserSelectField;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\Repository as ExcelImportRepository;
use srag\Plugins\SrUserEnrolment\ResetPassword\Repository as ResetUserPasswordRepository;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Repository as RuleEnrolmentRepository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Repository as RequiredDataRepository;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    use ConfigTrait {
        config as protected _config;
    }
    use Notifications4PluginTrait {
        notifications4plugin as protected _notifications4plugin;
    }
    use RequiredDataTrait {
        requiredData as protected _requiredData;
    }
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

            self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_EXTENDS_SRUSRENR);
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {
        $this->config()->withTableName(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_config")->withFields([
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
        ]);

        $this->notifications4plugin()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
            "request" => "object " . Request::class
        ]);

        $this->requiredData()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin());
        $this->requiredData()->fields()->factory()->addClass(UserSelectField::class);
    }


    /**
     * @inheritDoc
     */
    public function config() : ConfigRepository
    {
        return self::_config();
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        $this->config()->dropTables();
        $this->enrolmentWorkflow()->dropTables();
        $this->excelImport()->dropTables();
        $this->notifications4plugin()->dropTables();
        $this->requiredData()->dropTables();
        $this->resetUserPassword()->dropTables();
        $this->ruleEnrolment()->dropTables();
    }


    /**
     * @return EnrolmentWorkflowRepository
     */
    public function enrolmentWorkflow() : EnrolmentWorkflowRepository
    {
        return EnrolmentWorkflowRepository::getInstance();
    }


    /**
     * @return ExcelImportRepository
     */
    public function excelImport() : ExcelImportRepository
    {
        return ExcelImportRepository::getInstance();
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        $this->config()->installTables();
        $this->enrolmentWorkflow()->installTables();
        $this->excelImport()->installTables();
        $this->notifications4plugin()->installTables();
        $this->requiredData()->installTables();
        $this->resetUserPassword()->installTables();
        $this->ruleEnrolment()->installTables();
    }


    /**
     * @inheritDoc
     */
    public function notifications4plugin() : Notifications4PluginRepositoryInterface
    {
        return self::_notifications4plugin();
    }


    /**
     * @inheritDoc
     */
    public function requiredData() : RequiredDataRepository
    {
        return self::_requiredData();
    }


    /**
     * @return ResetUserPasswordRepository
     */
    public function resetUserPassword() : ResetUserPasswordRepository
    {
        return ResetUserPasswordRepository::getInstance();
    }


    /**
     * @return RuleEnrolmentRepository
     */
    public function ruleEnrolment() : RuleEnrolmentRepository
    {
        return RuleEnrolmentRepository::getInstance();
    }


    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function userHasRole(int $user_id) : bool
    {
        $user_roles = self::dic()->rbacreview()->assignedGlobalRoles($user_id);
        $config_roles = self::srUserEnrolment()->config()->getField(ConfigFormGUI::KEY_ROLES);

        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $config_roles)) {
                return true;
            }
        }

        return false;
    }
}
