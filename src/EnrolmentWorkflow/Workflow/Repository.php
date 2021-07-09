<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow
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
     * @param Workflow $workflow
     */
    public function deleteWorkflow(Workflow $workflow) : void
    {
        $workflow->delete();

        self::srUserEnrolment()->enrolmentWorkflow()->steps()->deleteSteps($workflow->getWorkflowId());
        self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->deleteSelectedWorkflows($workflow->getWorkflowId());
    }


    /**
     * @internal
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(Workflow::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $workflow_id
     *
     * @return Workflow|null
     */
    public function getWorkflowById(int $workflow_id) : ?Workflow
    {
        /**
         * @var Workflow|null $workflow
         */

        $workflow = Workflow::where(["workflow_id" => $workflow_id])->first();

        return $workflow;
    }


    /**
     * @param bool $only_enabled
     *
     * @return Workflow[]
     */
    public function getWorkflows(bool $only_enabled = true) : array
    {
        $where = Workflow::where([]);

        if ($only_enabled) {
            $where = $where->where(["enabled" => true]);
        }

        return $where->orderBy("title", "asc")->get();
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        Workflow::updateDB();
    }


    /**
     * @param Workflow $workflow
     */
    public function storeWorkflow(Workflow $workflow) : void
    {
        $workflow->store();

        if (!$workflow->isEnabled()) {
            self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->deleteSelectedWorkflows($workflow->getWorkflowId());
        }
    }
}
