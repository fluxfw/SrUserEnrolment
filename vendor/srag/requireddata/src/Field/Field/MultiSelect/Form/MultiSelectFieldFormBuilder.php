<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\MultiSelect\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\MultiSelect\MultiSelectField;
use srag\RequiredData\SrUserEnrolment\Field\Field\Select\Form\SelectFieldFormBuilder;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class MultiSelectFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\MultiSelect\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiSelectFieldFormBuilder extends SelectFieldFormBuilder
{

    /**
     * @var MultiSelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, MultiSelectField $field)
    {
        parent::__construct($parent, $field);
    }
}
