<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow;

use ilDBConstants;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\Repository as ActionsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\Repository as AssistantsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\Repository as DeputiesRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Repository as MembersRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Repository as RequestsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Repository as RulesRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\Repository as SelectedWorkflowsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Repository as StepsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Repository as WorkflowsRepository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


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
     * @return ActionsRepository
     */
    public function actions() : ActionsRepository
    {
        return ActionsRepository::getInstance();
    }


    /**
     * @return AssistantsRepository
     */
    public function assistants() : AssistantsRepository
    {
        return AssistantsRepository::getInstance();
    }


    /**
     * @return DeputiesRepository
     */
    public function deputies() : DeputiesRepository
    {
        return DeputiesRepository::getInstance();
    }


    /**
     * @internal
     */
    public function dropTables() : void
    {
        $this->actions()->dropTables();
        $this->assistants()->dropTables();
        $this->deputies()->dropTables();
        $this->members()->dropTables();
        $this->requests()->dropTables();
        $this->rules()->dropTables();
        $this->selectedWorkflows()->dropTables();
        $this->steps()->dropTables();
        $this->workflows()->dropTables();
    }


    /**
     * @param int|null $user_id
     * @param bool     $plugin_active_check
     *
     * @return bool
     */
    public function hasAccess(/*?*/ int $user_id = null, bool $plugin_active_check = true) : bool
    {
        if (empty($user_id)) {
            // TODO: Remove if no CtrlMainMenu
            $user_id = self::dic()->user()->getId();
        }

        if (!$this->isEnabled($plugin_active_check)) {
            return false;
        }

        return self::dic()
            ->access()
            ->checkAccessOfUser($user_id, "write", "", self::dic()
                                                           ->database()
                                                           ->queryF('SELECT ref_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id WHERE type=%s',
                                                               [ilDBConstants::T_TEXT], ["cmps"])
                                                           ->fetchAssoc()["ref_id"]);
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        $this->actions()->installTables();
        $this->assistants()->installTables();
        $this->deputies()->installTables();
        $this->members()->installTables();
        $this->requests()->installTables();
        $this->rules()->installTables();
        $this->selectedWorkflows()->installTables();
        $this->steps()->installTables();
        $this->workflows()->installTables();
    }


    /**
     * @param bool $plugin_active_check
     *
     * @return bool
     */
    public function isEnabled(bool $plugin_active_check = true) : bool
    {
        return (($plugin_active_check ? self::plugin()->getPluginObject()->isActive() : true) && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_ENROLMENT_WORKFLOW));
    }


    /**
     * @return MembersRepository
     */
    public function members() : MembersRepository
    {
        return MembersRepository::getInstance();
    }


    /**
     * @return RequestsRepository
     */
    public function requests() : RequestsRepository
    {
        return RequestsRepository::getInstance();
    }


    /**
     * @return RulesRepository
     */
    public function rules() : RulesRepository
    {
        return RulesRepository::getInstance();
    }


    /**
     * @return SelectedWorkflowsRepository
     */
    public function selectedWorkflows() : SelectedWorkflowsRepository
    {
        return SelectedWorkflowsRepository::getInstance();
    }


    /**
     * @return StepsRepository
     */
    public function steps() : StepsRepository
    {
        return StepsRepository::getInstance();
    }


    /**
     * @return WorkflowsRepository
     */
    public function workflows() : WorkflowsRepository
    {
        return WorkflowsRepository::getInstance();
    }
}
