<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Utils;

use srag\DataTableUI\SrUserEnrolment\Component\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DataTableUITrait
{

    /**
     * @return FactoryInterface
     */
    protected static function dataTableUI() : FactoryInterface
    {
        return Factory::getInstance();
    }
}
