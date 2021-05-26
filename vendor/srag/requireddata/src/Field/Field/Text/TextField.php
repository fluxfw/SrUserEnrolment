<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Text;

use srag\RequiredData\SrUserEnrolment\Field\AbstractField;

/**
 * Class TextField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Text
 */
class TextField extends AbstractField
{

    const TABLE_NAME_SUFFIX = "txt";


    /**
     * @inheritDoc
     */
    public function getFieldDescription() : string
    {
        return "";
    }


    /**
     * @inheritDoc
     */
    public function supportsMultiLang() : bool
    {
        return true;
    }
}
