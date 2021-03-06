<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\SearchSelect\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\MultiSearchSelect\Form\MultiSearchSelectFieldFormBuilder;
use srag\RequiredData\SrUserEnrolment\Field\Field\SearchSelect\SearchSelectField;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class SearchSelectFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\SearchSelect\Form
 */
class SearchSelectFieldFormBuilder extends MultiSearchSelectFieldFormBuilder
{

    /**
     * @var SearchSelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, SearchSelectField $field)
    {
        parent::__construct($parent, $field);
    }
}
