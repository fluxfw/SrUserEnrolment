<?php

namespace srag\Notifications4Plugin\SrUserEnrolment\Utils;

use srag\Notifications4Plugin\SrUserEnrolment\Repository as Notifications4PluginRepository;
use srag\Notifications4Plugin\SrUserEnrolment\RepositoryInterface as Notifications4PluginRepositoryInterface;

/**
 * Trait Notifications4PluginTrait
 *
 * @package srag\Notifications4Plugin\SrUserEnrolment\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait Notifications4PluginTrait
{

    /**
     * @return Notifications4PluginRepositoryInterface
     */
    protected static function notifications4plugin() : Notifications4PluginRepositoryInterface
    {
        return Notifications4PluginRepository::getInstance();
    }
}
