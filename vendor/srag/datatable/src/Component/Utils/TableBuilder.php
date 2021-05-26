<?php

namespace srag\DataTableUI\SrUserEnrolment\Component\Utils;

use srag\DataTableUI\SrUserEnrolment\Component\Table;

/**
 * Interface TableBuilder
 *
 * @package srag\DataTableUI\SrUserEnrolment\Component\Utils
 */
interface TableBuilder
{

    /**
     * @return Table
     */
    public function getTable() : Table;


    /**
     * @return string
     */
    public function render() : string;
}
