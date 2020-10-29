<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Sort;

use srag\DataTableUI\SrUserEnrolment\Component\Settings\Sort\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Sort\SortField as SortFieldInterface;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Settings\Sort
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function sortField(string $sort_field, int $sort_field_direction) : SortFieldInterface
    {
        return new SortField($sort_field, $sort_field_direction);
    }
}
