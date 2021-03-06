<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Checkbox;

use srag\RequiredData\SrUserEnrolment\Field\AbstractField;

/**
 * Class CheckboxField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Checkbox
 */
class CheckboxField extends AbstractField
{

    const TABLE_NAME_SUFFIX = "chck";


    /**
     * @inheritDoc
     */
    public function getFieldDescription() : string
    {
        return "";
    }
}
