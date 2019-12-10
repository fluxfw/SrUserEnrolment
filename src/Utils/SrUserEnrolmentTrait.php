<?php

namespace srag\Plugins\SrUserEnrolment\Utils;

use srag\Plugins\SrUserEnrolment\Repository;

/**
 * Trait SrUserEnrolmentTrait
 *
 * @package srag\Plugins\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
