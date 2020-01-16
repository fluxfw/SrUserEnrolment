<?php

namespace srag\ActiveRecordConfig\SrUserEnrolment\Utils;

use srag\ActiveRecordConfig\SrUserEnrolment\Config\Repository as ConfigRepository;

/**
 * Trait ConfigTrait
 *
 * @package srag\ActiveRecordConfig\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait ConfigTrait
{

    /**
     * @return ConfigRepository
     */
    protected static function config() : ConfigRepository
    {
        return ConfigRepository::getInstance();
    }
}
