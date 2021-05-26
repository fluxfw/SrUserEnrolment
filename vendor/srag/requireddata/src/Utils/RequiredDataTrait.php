<?php

namespace srag\RequiredData\SrUserEnrolment\Utils;

use srag\RequiredData\SrUserEnrolment\Repository as RequiredDataRepository;

/**
 * Trait RequiredDataTrait
 *
 * @package srag\RequiredData\SrUserEnrolment\Utils
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
