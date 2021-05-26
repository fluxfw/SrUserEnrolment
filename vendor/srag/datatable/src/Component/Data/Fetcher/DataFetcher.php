<?php

namespace srag\DataTableUI\SrUserEnrolment\Component\Data\Fetcher;

use srag\DataTableUI\SrUserEnrolment\Component\Data\Data;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Settings;
use srag\DataTableUI\SrUserEnrolment\Component\Table;

/**
 * Interface DataFetcher
 *
 * @package srag\DataTableUI\SrUserEnrolment\Component\Data\Fetcher
 */
interface DataFetcher
{

    /**
     * @param Settings $settings
     *
     * @return Data
     */
    public function fetchData(Settings $settings) : Data;


    /**
     * @param Table $component
     *
     * @return string
     */
    public function getNoDataText(Table $component) : string;


    /**
     * @return bool
     */
    public function isFetchDataNeedsFilterFirstSet() : bool;
}
