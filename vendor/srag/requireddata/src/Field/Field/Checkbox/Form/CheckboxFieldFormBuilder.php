<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Checkbox\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\Checkbox\CheckboxField;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Form\AbstractFieldFormBuilder;

/**
 * Class CheckboxFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Checkbox\Form
 */
class CheckboxFieldFormBuilder extends AbstractFieldFormBuilder
{

    /**
     * @var CheckboxField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, CheckboxField $field)
    {
        parent::__construct($parent, $field);
    }
}
