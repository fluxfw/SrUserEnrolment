<?php

namespace srag\RequiredData\SrUserEnrolment\Field\DynamicValue;

use srag\RequiredData\SrUserEnrolment\Field\AbstractField;
use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;

/**
 * Class DynamicValueField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\DynamicValue
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class DynamicValueField extends AbstractField
{

    /**
     * @inheritDoc
     */
    public function getFieldDescription() : string
    {
        return self::requiredData()->getPlugin()->translate("dynamic_value", FieldsCtrl::LANG_MODULE, [$this->deliverDynamicValue()]);
    }


    /**
     * @return string
     */
    public abstract function deliverDynamicValue() : string;
}
