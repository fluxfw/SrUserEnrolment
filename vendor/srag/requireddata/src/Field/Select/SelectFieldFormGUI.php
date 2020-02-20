<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Select;

use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\TabsInputGUI;
use srag\RequiredData\SrUserEnrolment\Field\AbstractFieldFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class SelectFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Select
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SelectFieldFormGUI extends AbstractFieldFormGUI
{

    /**
     * @var SelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, SelectField $field)
    {
        parent::__construct($parent, $field);
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            [
                "options" => [
                    self::PROPERTY_CLASS    => MultiLineNewInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => [
                        "label" => [
                            self::PROPERTY_CLASS    => TabsInputGUI::class,
                            self::PROPERTY_REQUIRED => true,
                            self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                                "label" => [
                                    self::PROPERTY_CLASS => ilTextInputGUI::class
                                ]
                            ], true)
                        ],
                        "value" => [
                            self::PROPERTY_CLASS    => ilTextInputGUI::class,
                            self::PROPERTY_REQUIRED => true
                        ]
                    ]
                ]
            ]
        );
    }
}
