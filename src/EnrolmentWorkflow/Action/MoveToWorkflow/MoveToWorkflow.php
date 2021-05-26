<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;

/**
 * Class MoveToWorkflow
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow
 */
class MoveToWorkflow extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "mvtwf";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $move_to_step_id = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $move_to_workflow_id = 0;


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        $descriptions = [];

        $workflow = self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflowById($this->move_to_workflow_id);
        if ($workflow !== null) {
            $descriptions[] = $workflow->getTitle();
        }

        $step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById($this->move_to_step_id);
        if ($step !== null) {
            $descriptions[] = $step->getTitle();
        }

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
    }


    /**
     * @inheritDoc
     */
    public function getInitRunNextActions() : bool
    {
        return false;
    }


    /**
     * @return int
     */
    public function getMoveToStepId() : int
    {
        return $this->move_to_step_id;
    }


    /**
     * @param int $move_to_step_id
     */
    public function setMoveToStepId(int $move_to_step_id)/* : void*/
    {
        $this->move_to_step_id = $move_to_step_id;
    }


    /**
     * @return int
     */
    public function getMoveToWorkflowId() : int
    {
        return $this->move_to_workflow_id;
    }


    /**
     * @param int $move_to_workflow_id
     */
    public function setMoveToWorkflowId(int $move_to_workflow_id)/* : void*/
    {
        $this->move_to_workflow_id = $move_to_workflow_id;
    }
}
