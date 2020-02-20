<?php

namespace srag\CommentsUI\SrUserEnrolment\Utils;

use srag\CommentsUI\SrUserEnrolment\Comment\Repository;
use srag\CommentsUI\SrUserEnrolment\Comment\RepositoryInterface;
use srag\CommentsUI\SrUserEnrolment\UI\UI;
use srag\CommentsUI\SrUserEnrolment\UI\UIInterface;

/**
 * Trait CommentsUITrait
 *
 * @package srag\CommentsUI\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait CommentsUITrait
{

    /**
     * @return RepositoryInterface
     */
    protected static function comments() : RepositoryInterface
    {
        return Repository::getInstance();
    }


    /**
     * @return UIInterface
     */
    protected static function commentsUI() : UIInterface
    {
        return new UI();
    }
}
