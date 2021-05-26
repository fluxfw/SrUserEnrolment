<?php

namespace srag\Plugins\SrUserEnrolment;

use ilObject;
use ilObjectFactory;
use ilSrUserEnrolmentPlugin;
use srag\CommentsUI\SrUserEnrolment\Comment\Comment;
use srag\CommentsUI\SrUserEnrolment\Comment\RepositoryInterface as CommentsRepositoryInterface;
use srag\CommentsUI\SrUserEnrolment\UI\UIInterface as CommentsUIRepositoryInterface;
use srag\CommentsUI\SrUserEnrolment\Utils\CommentsUITrait;
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
use srag\Plugins\SrUserEnrolment\Log\Repository as LogsRepository;
use srag\Plugins\SrUserEnrolment\Menu\Menu;
use srag\Plugins\SrUserEnrolment\ResetPassword\Repository as ResetUserPasswordRepository;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Repository as RuleEnrolmentRepository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Repository as RequiredDataRepository;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment
 */
final class Repository
{

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    use DICTrait;
    use SrUserEnrolmentTrait;
    use CommentsUITrait {
        comments as protected _comments;
        commentsUI as protected _commentsUI;
    }
    use Notifications4PluginTrait {
        notifications4plugin as protected _notifications4plugin;
    }
    use RequiredDataTrait {
        requiredData as protected _requiredData;
    }

    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var ilObject[]
     */
    protected $il_objs_by_obj_id = [];
    /**
     * @var ilObject[]
     */
    protected $il_objs_by_obj_ref_id = [];


    /**
     * Repository constructor
     */
    private function __construct()
    {
        $this->comments()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin())->withShareMethod(Comment::SHARE_METHOD_AUTO);

        $this->notifications4plugin()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
            "request" => "object " . Request::class
        ]);

        $this->requiredData()->withTableNamePrefix(ilSrUserEnrolmentPlugin::PLUGIN_ID)->withPlugin(self::plugin());
        $this->requiredData()->fields()->factory()->addClass(UserSelectField::class);
    }


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
     * @inheritDoc
     */
    public function comments() : CommentsRepositoryInterface
    {
        return self::_comments();
    }


    /**
     * @inheritDoc
     */
    public function commentsUI() : CommentsUIRepositoryInterface
    {
        return self::_commentsUI();
    }


    /**
     * @return ConfigRepository
     */
    public function config() : ConfigRepository
    {
        return ConfigRepository::getInstance();
    }


    /**
     * @param int $usr_id
     */
    public function deleteByUser(int $usr_id)/*:void*/
    {
        $this->enrolmentWorkflow()->assistants()->deleteUserAssistants($usr_id);
        $this->enrolmentWorkflow()->deputies()->deleteUserDeputies($usr_id);
        $this->comments()->deleteUserComments($usr_id);
        $this->logs()->deleteUserLogs($usr_id);
        $this->enrolmentWorkflow()->members()->deleteUserMembers($usr_id);
        $this->enrolmentWorkflow()->requests()->deleteUserRequests($usr_id);
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        $this->comments()->dropTables();
        $this->config()->dropTables();
        $this->enrolmentWorkflow()->dropTables();
        $this->excelImport()->dropTables();
        $this->jobs()->dropTables();
        $this->logs()->dropTables();
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
     * @param int $obj_id
     *
     * @return ilObject
     */
    public function getIliasObjectById(int $obj_id) : ilObject
    {
        $obj = $this->il_objs_by_obj_id[$obj_id];

        if ($obj === null) {
            $obj = ilObjectFactory::getInstanceByObjId($obj_id, false);

            $this->il_objs_by_obj_id[$obj_id] = $obj;
        }

        return $obj;
    }


    /**
     * @param int $obj_ref_id
     *
     * @return ilObject
     */
    public function getIliasObjectByRefId(int $obj_ref_id) : ilObject
    {
        $obj = $this->il_objs_by_obj_ref_id[$obj_ref_id];

        if ($obj === null) {
            $obj = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);

            $this->il_objs_by_obj_ref_id[$obj_ref_id] = $obj;

            $this->il_objs_by_obj_id[$obj->getId()] = $obj;
        }

        return $obj;
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        $this->comments()->installTables();
        $this->config()->installTables();
        $this->enrolmentWorkflow()->installTables();
        $this->excelImport()->installTables();
        $this->jobs()->installTables();
        $this->logs()->installTables();
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
     * @return LogsRepository
     */
    public function logs() : LogsRepository
    {
        return LogsRepository::getInstance();
    }


    /**
     * @return Menu
     */
    public function menu() : Menu
    {
        return new Menu(self::dic()->dic(), self::plugin()->getPluginObject());
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
        $user_roles = self::dic()->rbac()->review()->assignedGlobalRoles($user_id);
        $config_roles = self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_ROLES);

        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $config_roles)) {
                return true;
            }
        }

        return false;
    }
}
