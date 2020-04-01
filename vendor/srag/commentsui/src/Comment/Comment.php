<?php

namespace srag\CommentsUI\SrUserEnrolment\Comment;

use JsonSerializable;
use stdClass;

/**
 * Interface Comment
 *
 * @package srag\CommentsUI\SrUserEnrolment\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Comment extends JsonSerializable
{

    const SHARE_METHOD_DISABLED = 1;
    const SHARE_METHOD_ENABLED = 2;
    const SHARE_METHOD_AUTO = 3;


    /**
     * @return int
     */
    public function getId() : int;


    /**
     * @param int $id
     */
    public function setId(int $id)/* : void*/;


    /**
     * @return string
     */
    public function getComment() : string;


    /**
     * @param string $comment
     */
    public function setComment(string $comment)/* : void*/;


    /**
     * @return int
     */
    public function getReportObjId() : int;


    /**
     * @param int $report_obj_id
     */
    public function setReportObjId(int $report_obj_id)/* : void*/;


    /**
     * @return int
     */
    public function getReportUserId() : int;


    /**
     * @param int $report_user_id
     */
    public function setReportUserId(int $report_user_id)/* : void*/;


    /**
     * @return int
     */
    public function getCreatedTimestamp() : int;


    /**
     * @param int $created_timestamp
     */
    public function setCreatedTimestamp(int $created_timestamp)/* : void*/;


    /**
     * @return int
     */
    public function getCreatedUserId() : int;


    /**
     * @param int $created_user_id
     */
    public function setCreatedUserId(int $created_user_id)/* : void*/;


    /**
     * @return int
     */
    public function getUpdatedTimestamp() : int;


    /**
     * @param int $updated_timestamp
     */
    public function setUpdatedTimestamp(int $updated_timestamp)/* : void*/;


    /**
     * @return int
     */
    public function getUpdatedUserId() : int;


    /**
     * @param int $updated_user_id
     */
    public function setUpdatedUserId(int $updated_user_id)/* : void*/;


    /**
     * @return bool
     */
    public function isShared() : bool;


    /**
     * @param bool $is_shared
     */
    public function setIsShared(bool $is_shared)/* : void*/;


    /**
     * @return bool
     */
    public function isDeleted() : bool;


    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted)/* : void*/;


    /**
     * @return stdClass
     */
    public function jsonSerialize() : stdClass;
}
