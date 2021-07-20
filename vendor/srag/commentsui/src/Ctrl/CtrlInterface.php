<?php

namespace srag\CommentsUI\SrUserEnrolment\Ctrl;

/**
 * Interface CtrlInterface
 *
 * @package srag\CommentsUI\SrUserEnrolment\Ctrl
 */
interface CtrlInterface
{

    const CMD_CREATE_COMMENT = "createComment";
    const CMD_DELETE_COMMENT = "deleteComment";
    const CMD_GET_COMMENTS = "getComments";
    const CMD_SHARE_COMMENT = "shareComment";
    const CMD_UPDATE_COMMENT = "updateComment";
    const GET_PARAM_COMMENT_ID = "comment_id";
    const GET_PARAM_REPORT_OBJ_ID = "report_obj_id";
    const GET_PARAM_REPORT_USER_ID = "report_user_id";


    /**
     *
     */
    public function executeCommand() : void;


    /**
     * @return string
     */
    public function getAsyncBaseUrl() : string;


    /**
     * @return array
     */
    public function getAsyncClass() : array;


    /**
     * @param int $report_obj_id
     * @param int $report_user_id
     *
     * @return array
     */
    public function getCommentsArray(int $report_obj_id, int $report_user_id) : array;


    /**
     * @return bool
     */
    public function getIsReadOnly() : bool;
}
