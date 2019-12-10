<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Checkbox;

use srag\RequiredData\SrUserEnrolment\Field\AbstractField;

/**
 * Class CheckboxField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Checkbox
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
