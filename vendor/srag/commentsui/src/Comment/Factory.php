<?php

namespace srag\CommentsUI\SrUserEnrolment\Comment;

use ilDateTime;
use srag\DIC\SrUserEnrolment\DICTrait;
use stdClass;

/**
 * Class Factory
 *
 * @package srag\CommentsUI\SrUserEnrolment\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory implements FactoryInterface
{

    use DICTrait;

    /**
     * @var FactoryInterface|null
     */
    protected static $instance = null;


    /**
     * @return FactoryInterface
     */
    public static function getInstance() : FactoryInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function fromDB(stdClass $data) : Comment
    {
        $comment = $this->newInstance();

        $comment->setId($data->id);
        $comment->setComment($data->comment);
        $comment->setReportObjId($data->report_obj_id);
        $comment->setReportUserId($data->report_user_id);
        $comment->setCreatedTimestamp((new ilDateTime($data->created_timestamp, IL_CAL_DATETIME))->getUnixTime());
        $comment->setCreatedUserId($data->created_user_id);
        $comment->setUpdatedTimestamp((new ilDateTime($data->updated_timestamp, IL_CAL_DATETIME))->getUnixTime());
        $comment->setUpdatedUserId($data->updated_user_id);
        $comment->setIsShared($data->is_shared);
        $comment->setDeleted($data->deleted);

        return $comment;
    }


    /**
     * @inheritDoc
     */
    public function newInstance() : Comment
    {
        $comment = new CommentAR();

        return $comment;
    }
}
