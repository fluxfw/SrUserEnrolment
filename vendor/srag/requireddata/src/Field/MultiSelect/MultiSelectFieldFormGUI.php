<?php

namespace srag\RequiredData\SrUserEnrolment\Field\MultiSelect;

use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Select\SelectFieldFormGUI;

/**
 * Class MultiSelectFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\MultiSelect
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiSelectFieldFormGUI extends SelectFieldFormGUI
{

    /**
     * @var MultiSelectField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, MultiSelectField $object)
    {
        parent::__construct($parent, $object);
    }
}
