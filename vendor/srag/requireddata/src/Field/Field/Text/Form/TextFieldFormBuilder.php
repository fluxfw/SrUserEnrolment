<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Text\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\Text\TextField;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Form\AbstractFieldFormBuilder;

/**
 * Class TextFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Text\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TextFieldFormBuilder extends AbstractFieldFormBuilder
{

    /**
     * @var TextField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, TextField $field)
    {
        parent::__construct($parent, $field);
    }
}
