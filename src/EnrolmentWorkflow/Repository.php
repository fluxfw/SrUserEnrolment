<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\Repository as ActionRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Repository as RequestRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Repository as RuleRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\Repository as SelectedWorkflowRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Repository as StepRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Repository as WorkflowRepository;
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
     * @return ActionRepository
     */
    public function actions() : ActionRepository
    {
        return ActionRepository::getInstance();
    }


    /**
     * @internal
     */
    public function dropTables()/*: void*/
    {
        $this->actions()->dropTables();
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
        return (($plugin_active_check ? self::plugin()->getPluginObject()->isActive() : true) && Config::getField(Config::KEY_SHOW_ENROLMENT_WORKFLOW));
    }


    /**
     * @return RequestRepository
     */
    public function requests() : RequestRepository
    {
        return RequestRepository::getInstance();
    }


    /**
     * @return RuleRepository
     */
    public function rules() : RuleRepository
    {
        return RuleRepository::getInstance();
    }


    /**
     * @return SelectedWorkflowRepository
     */
    public function selectedWorkflows() : SelectedWorkflowRepository
    {
        return SelectedWorkflowRepository::getInstance();
    }


    /**
     * @return StepRepository
     */
    public function steps() : StepRepository
    {
        return StepRepository::getInstance();
    }


    /**
     * @return WorkflowRepository
     */
    public function workflows() : WorkflowRepository
    {
        return WorkflowRepository::getInstance();
    }
}
