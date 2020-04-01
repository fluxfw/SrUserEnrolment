<?php

namespace srag\CommentsUI\SrUserEnrolment\Ctrl;

use srag\CommentsUI\SrUserEnrolment\Utils\CommentsUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class AbstractCtrl
 *
 * @package srag\CommentsUI\SrUserEnrolment\Ctrl
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractCtrl implements CtrlInterface
{

    use DICTrait;
    use CommentsUITrait;


    /**
     * AbstractCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function executeCommand()/* : void*/
    {
        $cmd = self::dic()->ctrl()->getCmd();

        switch ($cmd) {
            case self::CMD_CREATE_COMMENT:
            case self::CMD_DELETE_COMMENT:
            case self::CMD_GET_COMMENTS:
            case self::CMD_SHARE_COMMENT:
            case self::CMD_UPDATE_COMMENT:
                $this->{$cmd}();
                break;

            default:
                break;
        }
    }


    /**
     *
     */
    protected function getComments()/* : void*/
    {
        $report_obj_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REPORT_OBJ_ID));
        $report_user_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REPORT_USER_ID));

        self::output()->outputJSON($this->getCommentsArray($report_obj_id, $report_user_id));
    }


    /**
     *
     */
    protected function createComment()/* : void*/
    {
        $report_obj_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REPORT_OBJ_ID));
        $report_user_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REPORT_USER_ID));

        $comment = self::comments()->factory()->newInstance();

        $comment->setReportObjId($report_obj_id);

        $comment->setReportUserId($report_user_id);

        $comment->setComment(filter_input(INPUT_POST, "content"));

        self::comments()->storeComment($comment);

        self::output()->outputJSON($comment);
    }


    /**
     *
     */
    protected function updateComment()/* : void*/
    {
        $comment_id = intval(filter_input(INPUT_GET, self::GET_PARAM_COMMENT_ID));

        $comment = self::comments()->getCommentById($comment_id);

        $comment->setComment(filter_input(INPUT_POST, "content"));

        self::comments()->storeComment($comment);

        self::output()->outputJSON($comment);
    }


    /**
     *
     */
    protected function deleteComment()/* : void*/
    {
        $comment_id = intval(filter_input(INPUT_GET, self::GET_PARAM_COMMENT_ID));

        $comment = self::comments()->getCommentById($comment_id);

        self::comments()->deleteComment($comment);
    }


    /**
     *
     */
    protected function shareComment()/* : void*/
    {
        $comment_id = intval(filter_input(INPUT_GET, self::GET_PARAM_COMMENT_ID));

        $comment = self::comments()->getCommentById($comment_id);

        self::comments()->shareComment($comment);

        self::output()->outputJSON($comment);
    }


    /**
     * @inheritDoc
     */
    public function getIsReadOnly() : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    public function getAsyncBaseUrl() : string
    {
        return self::dic()->ctrl()->getLinkTargetByClass($this->getAsyncClass(), "", "", true, false);
    }
}
