<?php

namespace srag\Plugins\SrUserEnrolment\Utils;

use srag\Plugins\SrUserEnrolment\Repository;

/**
 * Trait SrUserEnrolmentTrait
 *
 * @package srag\Plugins\SrUserEnrolment\Utils
 */
trait SrUserEnrolmentTrait
{

    /**
     * @return Repository
     */
    protected static function srUserEnrolment() : Repository
    {
        return Repository::getInstance();
    }
}
