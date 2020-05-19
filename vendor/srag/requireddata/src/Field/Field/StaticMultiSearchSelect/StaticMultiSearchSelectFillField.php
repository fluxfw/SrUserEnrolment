<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect;

use srag\RequiredData\SrUserEnrolment\Field\Field\MultiSearchSelect\MultiSearchSelectFillField;

/**
 * Class StaticMultiSearchSelectFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class StaticMultiSearchSelectFillField extends MultiSearchSelectFillField
{

    /**
     * @var StaticMultiSearchSelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(StaticMultiSearchSelectField $field)
    {
        parent::__construct($field);
    }
}
