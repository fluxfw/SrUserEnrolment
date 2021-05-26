<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Utils;

use srag\DataTableUI\SrUserEnrolment\Component\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Utils
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
