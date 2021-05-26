<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Float;

use ILIAS\UI\Component\Input\Field\Input;
use srag\RequiredData\SrUserEnrolment\Field\Field\Integer\IntegerFillField;

/**
 * Class FloatFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\Float
 */
class FloatFillField extends IntegerFillField
{

    /**
     * @var FloatField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FloatField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function formatAsJson($fill_value)
    {
        return floatval($fill_value);
    }


    /**
     * @inheritDoc
     */
    public function getInput() : Input
    {
        $input = parent::getInput();

        $input->getInput()->allowDecimals(true);

        if ($this->field->getCountDecimals() !== null) {
            $input->getInput()->setDecimals($this->field->getCountDecimals());
        }

        return $input;
    }
}
