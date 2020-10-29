<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Data\Row;

use srag\DataTableUI\SrUserEnrolment\Component\Data\Row\Factory as FactoryInterface;
use srag\DataTableUI\SrUserEnrolment\Component\Data\Row\RowData;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Data\Row
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
    public function getter(string $row_id, /*object*/ $original_data) : RowData
    {
        return new GetterRowData($row_id, $original_data);
    }


    /**
     * @inheritDoc
     */
    public function property(string $row_id, /*object*/ $original_data) : RowData
    {
        return new PropertyRowData($row_id, $original_data);
    }
}
