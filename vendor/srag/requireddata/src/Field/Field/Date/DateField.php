<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Date;

use srag\RequiredData\SrUserEnrolment\Field\AbstractField;

/**
 * Class DateField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Date
 */
class DateField extends AbstractField
{

    const TABLE_NAME_SUFFIX = "dat";


    /**
     * @inheritDoc
     */
    public function getFieldDescription() : string
    {
        return "";
    }
}
