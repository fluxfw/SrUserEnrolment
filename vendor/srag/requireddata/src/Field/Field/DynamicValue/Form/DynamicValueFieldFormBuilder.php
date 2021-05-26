<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\DynamicValue\Form;

use srag\RequiredData\SrUserEnrolment\Field\Field\DynamicValue\DynamicValueField;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Form\AbstractFieldFormBuilder;

/**
 * Class DynamicValueFieldFormBuilder
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\DynamicValue\Form
 */
abstract class DynamicValueFieldFormBuilder extends AbstractFieldFormBuilder
{

    /**
     * @var DynamicValueField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, DynamicValueField $field)
    {
        parent::__construct($parent, $field);
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = parent::getFields();

        $fields += [
            "hide" => self::dic()->ui()->factory()->input()->field()->checkbox(self::requiredData()->getPlugin()->translate("hide", FieldsCtrl::LANG_MODULE),
                self::requiredData()->getPlugin()->translate("hide_info", FieldsCtrl::LANG_MODULE))
        ];

        return $fields;
    }
}
