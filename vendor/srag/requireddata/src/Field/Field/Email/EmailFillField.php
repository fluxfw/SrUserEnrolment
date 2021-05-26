<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Email;

use srag\RequiredData\SrUserEnrolment\Field\Field\Text\TextFillField;

/**
 * Class EmailFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Email
 */
class EmailFillField extends TextFillField
{

    /**
     * @var EmailField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(EmailField $field)
    {
        parent::__construct($field);
    }
}
