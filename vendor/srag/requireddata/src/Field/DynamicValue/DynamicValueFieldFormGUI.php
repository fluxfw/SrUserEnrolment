<?php

namespace srag\RequiredData\SrUserEnrolment\Field\DynamicValue;

use srag\RequiredData\SrUserEnrolment\Field\AbstractFieldFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class DynamicValueFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\DynamicValue
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class DynamicValueFieldFormGUI extends AbstractFieldFormGUI
{

    /**
     * @var DynamicValueField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, DynamicValueField $object)
    {
        parent::__construct($parent, $object);
    }
}
