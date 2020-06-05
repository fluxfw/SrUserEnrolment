<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\MultilineText\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\MultilineText\MultilineTextField;
use srag\RequiredData\SrUserEnrolment\Field\Field\Text\Form\TextFieldFormBuilder;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class MultilineTextFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\MultilineText\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultilineTextFieldFormBuilder extends TextFieldFormBuilder
{

    /**
     * @var MultilineTextField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, MultilineTextField $field)
    {
        parent::__construct($parent, $field);
    }
}
