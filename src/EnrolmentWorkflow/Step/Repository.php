<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
     * @param Step $step
     */
    public function deleteStep(Step $step)/*: void*/
    {
        $step->delete();

        $this->reSortSteps($step->getWorkflowId());

        self::srUserEnrolment()->enrolmentWorkflow()->rules()->deleteRules(AbstractRule::PARENT_CONTEXT_STEP, $step->getStepId());
        self::srUserEnrolment()->enrolmentWorkflow()->actions()->deleteActions($step->getStepId());
        self::srUserEnrolment()->requiredData()->fields()->deleteFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $step->getStepId());
        self::srUserEnrolment()->enrolmentWorkflow()->requests()->deleteRequests($step->getStepId());
    }


    /**
     * @param int $workflow_id
     */
    public function deleteSteps(int $workflow_id)/*: void*/
    {
        foreach ($this->getSteps($workflow_id, false) as $step) {
            $this->deleteStep($step);
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Step::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $step_id
     *
     * @return Step|null
     */
    public function getStepById(int $step_id)/*: ?Step*/
    {
        /**
         * @var Step|null $step
         */

        $step = Step::where(["step_id" => $step_id])->first();

        return $step;
    }


    /**
     * @param int|null $workflow_id
     * @param bool     $only_enabled
     *
     * @return Step[]
     */
    public function getSteps(/*?*/ int $workflow_id = null, bool $only_enabled = true) : array
    {
        if (!empty($workflow_id)) {
            $where = Step::where(["workflow_id" => $workflow_id])->orderBy("sort", "asc");
        } else {
            $where = Step::orderBy("title", "asc");
        }

        if ($only_enabled) {
            $where = $where->where(["enabled" => true]);
        }

        return $where->get();
    }


    /**
     * @param int          $type
     * @param int          $check_user_id
     * @param int          $request_user_id
     * @param int          $obj_ref_id
     * @param Request|null $request
     *
     * @return Step[]
     */
    public function getStepsForRequest(int $type, int $check_user_id, int $request_user_id, int $obj_ref_id,/*?*/ Request $request = null) : array
    {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->isEnabled()) {
            return [];
        }

        if ($request !== null) {
            $workflow_id = $request->getStep()->getWorkflowId();
        } else {
            $workflow_id = self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->getWorkflowId(self::dic()->objDataCache()->lookupObjId($obj_ref_id));
        }

        if (empty($workflow_id)) {
            return [];
        }

        $steps = array_filter($this->getSteps($workflow_id), function (Step $step) use ($type, $check_user_id, $request_user_id, $obj_ref_id, $request): bool {
            if (self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequest($obj_ref_id, $step->getStepId(), $request_user_id) !== null) {
                return false;
            }

            return (!empty(self::srUserEnrolment()->enrolmentWorkflow()
                ->rules()
                ->getCheckedRules(AbstractRule::PARENT_CONTEXT_STEP, $step->getStepId(), $type, $check_user_id, $obj_ref_id, false, $request)));
        });

        return $steps;
    }


    /**
     * @param Request $request
     * @param int     $check_user_id
     *
     * @return Step[]
     */
    public function getStepsForEditRequest(Request $request, int $check_user_id) : array
    {
        if ($request->isEdited()) {
            return [];
        }

        $steps = $this->getStepsForRequest(AbstractRule::TYPE_STEP_CHECK_ACTION, $check_user_id, $request->getUserId(), $request->getObjRefId(), $request);
        if (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId())) {
            foreach (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getDeputiesOf($check_user_id) as $deputy) {
                $steps += $this->getStepsForRequest(AbstractRule::TYPE_STEP_CHECK_ACTION, $deputy->getUserId(), $request->getUserId(), $request->getObjRefId(), $request);
            }
        }

        $steps = array_filter($steps, function (Step $step) use ($request, $check_user_id): bool {

            if ($request->getUserId() === $check_user_id) {
                return false;
            }

            if ($step->getStepId() === $request->getStepId()) {
                return false;
            }

            if ($step->getSort() < $request->getStep()->getSort()) {
                return false;
            }

            $step_request = self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequest($request->getObjRefId(), $step->getStepId(), $request->getUserId());

            if ($step_request !== null/* || $step_request->isEdited()*/) {
                return false;
            }

            return true;
        });

        return $steps;
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Step::updateDB();
    }


    /**
     * @param Step $step
     */
    public function moveStepUp(Step $step)/*: void*/
    {
        $step->setSort($step->getSort() - 15);

        $this->storeStep($step);

        $this->reSortSteps($step->getWorkflowId());
    }


    /**
     * @param Step $step
     */
    public function moveStepDown(Step $step)/*: void*/
    {
        $step->setSort($step->getSort() + 15);

        $this->storeStep($step);

        $this->reSortSteps($step->getWorkflowId());
    }


    /**
     * @param int $workflow_id
     */
    protected function reSortSteps(int $workflow_id)/*: void*/
    {
        $steps = $this->getSteps($workflow_id, false);

        $i = 1;
        foreach ($steps as $step) {
            $step->setSort($i * 10);

            $this->storeStep($step);

            $i++;
        }
    }


    /**
     * @param Step $step
     */
    public function storeStep(Step $step)/*: void*/
    {
        if (empty($step->getStepId())) {
            $step->setSort(((count($this->getSteps($step->getWorkflowId(), false)) + 1) * 10));
        }

        $step->store();
    }
}
