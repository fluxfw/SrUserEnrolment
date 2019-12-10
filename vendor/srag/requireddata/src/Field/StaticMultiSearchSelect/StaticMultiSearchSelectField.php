<?php

namespace srag\RequiredData\SrUserEnrolment\Field\StaticMultiSearchSelect;

use srag\RequiredData\SrUserEnrolment\Field\MultiSearchSelect\MultiSearchSelectField;

/**
 * Class StaticMultiSearchSelectField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\StaticMultiSearchSelect
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class StaticMultiSearchSelectField extends MultiSearchSelectField
{

    /**
     * @var string
     *
     * @abstract
     */
    const TABLE_NAME_SUFFIX = "";
}
