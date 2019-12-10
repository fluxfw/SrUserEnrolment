<?php

namespace srag\RequiredData\SrUserEnrolment\Utils;

use srag\RequiredData\SrUserEnrolment\Repository as RequiredDataRepository;

/**
 * Trait RequiredDataTrait
 *
 * @package srag\RequiredData\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait RequiredDataTrait
{

    /**
     * @return RequiredDataRepository
     */
    protected static function requiredData() : RequiredDataRepository
    {
        return RequiredDataRepository::getInstance();
    }
}
