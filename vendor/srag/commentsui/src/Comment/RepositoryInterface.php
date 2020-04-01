<?php

namespace srag\CommentsUI\SrUserEnrolment\Comment;

use srag\DIC\SrUserEnrolment\Plugin\Pluginable;
use stdClass;

/**
 * Interface RepositoryInterface
 *
 * @package srag\CommentsUI\SrUserEnrolment\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface RepositoryInterface extends Pluginable
{

    /**
     * @var int
     */
    const EDIT_LIMIT_MINUTES = 5;


    /**
     * @param Comment $comment
     *
     * @return bool
     */
    public function canBeDeleted(Comment $comment) : bool;


    /**
     * @param Comment $comment
     *
     * @return bool
     */
    public function canBeShared(Comment $comment) : bool;


    /**
     * @param Comment $comment
     *
     * @return bool
     */
    public function canBeStored(Comment $comment) : bool;


    /**
     * @param Comment $comment
     * @param bool    $check_can_be_deleted
     */
    public function deleteComment(Comment $comment, bool $check_can_be_deleted = true)/* : void*/;


    /**
     * @param int $report_user_id
     */
    public function deleteUserComments(int $report_user_id)/* : void*/;


    /**
     *
     */
    public function dropTables()/* : void*/;


    /**
     * @return FactoryInterface
     */
    public function factory() : FactoryInterface;


    /**
     * @param int $id
     *
     * @return Comment|null
     */
    public function getCommentById(int $id)/* : ?Comment*/;


    /**
     * @param int $report_obj_id
     * @param int $report_user_id
     *
     * @return Comment[]
     */
    public function getCommentsForReport(int $report_obj_id, int $report_user_id) : array;


    /**
     * @param int|null $report_obj_id
     * @param int|null $report_user_id
     *
     * @return Comment[]
     */
    public function getCommentsForCurrentUser(/*?int*/ $report_obj_id = null, /*?int*/ $report_user_id = null) : array;


    /**
     * @return int
     */
    public function getShareMethod() : int;


    /**
     * @return string
     */
    public function getTableNamePrefix() : string;


    /**
     *
     */
    public function installLanguages()/* : void*/;


    /**
     *
     */
    public function installTables()/* : void*/;


    /**
     * @param Comment $comment
     */
    public function shareComment(Comment $comment)/* : void*/;


    /**
     * @param Comment $comment
     * @param bool    $check_can_be_stored
     */
    public function storeComment(Comment $comment, bool $check_can_be_stored = true)/* : void*/;


    /**
     * @param Comment $comment
     *
     * @return stdClass
     */
    public function toJson(Comment $comment) : stdClass;


    /**
     * @param bool $output_object_titles
     *
     * @return self
     */
    public function withOutputObjectTitles(bool $output_object_titles = false) : self;


    /**
     * @param int $share_method
     *
     * @return self
     */
    public function withShareMethod(int $share_method = Comment::SHARE_METHOD_DISABLED) : self;


    /**
     * @param string $table_name_prefix
     *
     * @return self
     */
    public function withTableNamePrefix(string $table_name_prefix) : self;
}
