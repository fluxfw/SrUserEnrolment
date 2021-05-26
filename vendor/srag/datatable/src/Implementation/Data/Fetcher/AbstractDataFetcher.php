<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Data\Fetcher;

use srag\DataTableUI\SrUserEnrolment\Component\Data\Fetcher\DataFetcher;
use srag\DataTableUI\SrUserEnrolment\Component\Table;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class AbstractDataFetcher
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Data\Fetcher
 */
abstract class AbstractDataFetcher implements DataFetcher
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractDataFetcher constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getNoDataText(Table $component) : string
    {
        return $component->getPlugin()->translate("no_data", Table::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function isFetchDataNeedsFilterFirstSet() : bool
    {
        return false;
    }
}
