<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\Repository as ActionsRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\Repository as AssistantsRepository;
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
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
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
     * @internal
     */
    public function dropTables()/*: void*/
    {
        $this->actions()->dropTables();
        $this->assistants()->dropTables();
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

        return self::dic()->access()->checkAccessOfUser($user_id, "write", "", 31);
    }


    /**
     * @internal
     */
    public function installTables()/*: void*/
    {
        $this->actions()->installTables();
        $this->assistants()->installTables();
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
        return (($plugin_active_check ? self::plugin()->getPluginObject()->isActive() : true) && self::srUserEnrolment()->config()->getField(ConfigFormGUI::KEY_SHOW_ENROLMENT_WORKFLOW));
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
