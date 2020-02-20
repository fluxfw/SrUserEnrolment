<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Email;

use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Text\TextFieldFormGUI;

/**
 * Class EmailFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Email
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EmailFieldFormGUI extends TextFieldFormGUI
{

    /**
     * @var EmailField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, EmailField $field)
    {
        parent::__construct($parent, $field);
    }
}
