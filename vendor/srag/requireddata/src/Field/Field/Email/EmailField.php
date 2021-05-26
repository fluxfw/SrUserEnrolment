<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Email;

use srag\RequiredData\SrUserEnrolment\Field\Field\Text\TextField;

/**
 * Class EmailField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Email
 */
class EmailField extends TextField
{

    const TABLE_NAME_SUFFIX = "eml";


    /**
     * @inheritDoc
     */
    public function supportsMultiLang() : bool
    {
        return false;
    }
}
