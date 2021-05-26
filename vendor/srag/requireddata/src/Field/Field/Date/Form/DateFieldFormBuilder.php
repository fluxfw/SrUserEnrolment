<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Date\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\Date\DateField;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Form\AbstractFieldFormBuilder;

/**
 * Class DateFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Date\Form
 */
class DateFieldFormBuilder extends AbstractFieldFormBuilder
{

    /**
     * @var DateField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, DateField $field)
    {
        parent::__construct($parent, $field);
    }
}
