<?php

namespace srag\CommentsUI\SrUserEnrolment\Comment;

use ActiveRecord;
use arConnector;
use srag\CommentsUI\SrUserEnrolment\Utils\CommentsUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;
use stdClass;

/**
 * Class CommentAR
 *
 * @package srag\CommentsUI\SrUserEnrolment\Comment
 */
class CommentAR extends ActiveRecord implements Comment
{

    use DICTrait;
    use CommentsUITrait;

    const TABLE_NAME_SUFFIX = "com";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $comment = "";
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   timestamp
     * @con_is_notnull  true
     */
    protected $created_timestamp;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $created_user_id;
    /**
     * @var bool
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      1
     * @con_is_notnull  true
     */
    protected $deleted = false;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     * @con_is_primary  true
     */
    protected $id = 0;
    /**
     * @var bool
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      1
     * @con_is_notnull  true
     */
    protected $is_shared = false;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $report_obj_id;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $report_user_id;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   timestamp
     * @con_is_notnull  true
     */
    protected $updated_timestamp;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $updated_user_id;


    /**
     * CommentAR constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        //parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return string
     */
    public static function getTableName() : string
    {
        return self::comments()->getTableNamePrefix() . "_" . self::TABLE_NAME_SUFFIX;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return static::getTableName();
    }


    /**
     * @inheritDoc
     */
    public function getComment() : string
    {
        return $this->comment;
    }


    /**
     * @inheritDoc
     */
    public function setComment(string $comment) : void
    {
        $this->comment = $comment;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return static::getTableName();
    }


    /**
     * @inheritDoc
     */
    public function getCreatedTimestamp() : int
    {
        return $this->created_timestamp;
    }


    /**
     * @inheritDoc
     */
    public function setCreatedTimestamp(int $created_timestamp) : void
    {
        $this->created_timestamp = $created_timestamp;
    }


    /**
     * @inheritDoc
     */
    public function getCreatedUserId() : int
    {
        return $this->created_user_id;
    }


    /**
     * @inheritDoc
     */
    public function setCreatedUserId(int $created_user_id) : void
    {
        $this->created_user_id = $created_user_id;
    }


    /**
     * @inheritDoc
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @inheritDoc
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }


    /**
     * @inheritDoc
     */
    public function getReportObjId() : int
    {
        return $this->report_obj_id;
    }


    /**
     * @inheritDoc
     */
    public function setReportObjId(int $report_obj_id) : void
    {
        $this->report_obj_id = $report_obj_id;
    }


    /**
     * @inheritDoc
     */
    public function getReportUserId() : int
    {
        return $this->report_user_id;
    }


    /**
     * @inheritDoc
     */
    public function setReportUserId(int $report_user_id) : void
    {
        $this->report_user_id = $report_user_id;
    }


    /**
     * @inheritDoc
     */
    public function getUpdatedTimestamp() : int
    {
        return $this->updated_timestamp;
    }


    /**
     * @inheritDoc
     */
    public function setUpdatedTimestamp(int $updated_timestamp) : void
    {
        $this->updated_timestamp = $updated_timestamp;
    }


    /**
     * @inheritDoc
     */
    public function getUpdatedUserId() : int
    {
        return $this->updated_user_id;
    }


    /**
     * @inheritDoc
     */
    public function setUpdatedUserId(int $updated_user_id) : void
    {
        $this->updated_user_id = $updated_user_id;
    }


    /**
     * @inheritDoc
     */
    public function isDeleted() : bool
    {
        return $this->deleted;
    }


    /**
     * @inheritDoc
     */
    public function setDeleted(bool $deleted) : void
    {
        $this->deleted = $deleted;
    }


    /**
     * @inheritDoc
     */
    public function isShared() : bool
    {
        return $this->is_shared;
    }


    /**
     * @inheritDoc
     */
    public function jsonSerialize() : stdClass
    {
        return self::comments()->toJson($this);
    }


    /**
     * @inheritDoc
     */
    public function setIsShared(bool $is_shared) : void
    {
        $this->is_shared = $is_shared;
    }
}
