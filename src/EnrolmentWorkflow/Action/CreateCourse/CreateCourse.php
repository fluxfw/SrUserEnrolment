<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;

/**
 * Class CreateCourse
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateCourse extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "crcrs";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $field_course_end = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $field_course_start = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $field_course_title = "";
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $move_request = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $move_request_step_id = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $required_data_from_step_id = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $selected_workflow_id = 0;


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        return "";
    }


    /**
     * @return string
     */
    public function getFieldCourseEnd() : string
    {
        return $this->field_course_end;
    }


    /**
     * @param string $field_course_end
     */
    public function setFieldCourseEnd(string $field_course_end)/* : void*/
    {
        $this->field_course_end = $field_course_end;
    }


    /**
     * @return string
     */
    public function getFieldCourseStart() : string
    {
        return $this->field_course_start;
    }


    /**
     * @param string $field_course_start
     */
    public function setFieldCourseStart(string $field_course_start)/* : void*/
    {
        $this->field_course_start = $field_course_start;
    }


    /**
     * @return string
     */
    public function getFieldCourseTitle() : string
    {
        return $this->field_course_title;
    }


    /**
     * @param string $field_course_title
     */
    public function setFieldCourseTitle(string $field_course_title)/* : void*/
    {
        $this->field_course_title = $field_course_title;
    }


    /**
     * @return int
     */
    public function getMoveRequestStepId() : int
    {
        return $this->move_request_step_id;
    }


    /**
     * @param int $move_request_step_id
     */
    public function setMoveRequestStepId(int $move_request_step_id)/* : void*/
    {
        $this->move_request_step_id = $move_request_step_id;
    }


    /**
     * @return int
     */
    public function getRequiredDataFromStepId() : int
    {
        return $this->required_data_from_step_id;
    }


    /**
     * @param int $required_data_from_step_id
     */
    public function setRequiredDataFromStepId(int $required_data_from_step_id)/* : void*/
    {
        $this->required_data_from_step_id = $required_data_from_step_id;
    }


    /**
     * @return int
     */
    public function getSelectedWorkflowId() : int
    {
        return $this->selected_workflow_id;
    }


    /**
     * @param int $selected_workflow_id
     */
    public function setSelectedWorkflowId(int $selected_workflow_id)/* : void*/
    {
        $this->selected_workflow_id = $selected_workflow_id;
    }


    /**
     * @return bool
     */
    public function isMoveRequest() : bool
    {
        return $this->move_request;
    }


    /**
     * @param bool $move_request
     */
    public function setMoveRequest(bool $move_request)/* : void*/
    {
        $this->move_request = $move_request;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "move_request":
                return ($field_value ? 1 : 0);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "move_request":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
