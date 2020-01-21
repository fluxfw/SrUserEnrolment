<?php

namespace srag\Plugins\SrUserEnrolment;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Notifications4Plugin\SrUserEnrolment\RepositoryInterface as Notifications4PluginRepositoryInterface;
use srag\Notifications4Plugin\SrUserEnrolment\Utils\Notifications4PluginTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\Config\Repository as ConfigRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Repository as EnrolmentWorkflowRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect\UserSelectField;
use srag\Plugins\SrUserEnrolment\ExcelImport\Repository as ExcelImportRepository;
use srag\Plugins\SrUserEnrolment\Job\Repository as JobsRepository;
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
        $this->notifications4plugin()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
            "request" => "object " . Request::class
        ]);

        $this->requiredData()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin());
        $this->requiredData()->fields()->factory()->addClass(UserSelectField::class);
    }


    /**
     * @return ConfigRepository
     */
    public function config() : ConfigRepository
    {
        return ConfigRepository::getInstance();
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        $this->config()->dropTables();
        $this->enrolmentWorkflow()->dropTables();
        $this->excelImport()->dropTables();
        $this->jobs()->dropTables();
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
        $this->jobs()->installTables();
        $this->notifications4plugin()->installTables();
        $this->requiredData()->installTables();
        $this->resetUserPassword()->installTables();
        $this->ruleEnrolment()->installTables();
    }


    /**
     * @return JobsRepository
     */
    public function jobs() : JobsRepository
    {
        return JobsRepository::getInstance();
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
        $config_roles = self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_ROLES);

        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $config_roles)) {
                return true;
            }
        }

        return false;
    }
}
