<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Radio;

use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Field\Select\SelectFieldFormGUI;

/**
 * Class RadioFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Radio
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RadioFieldFormGUI extends SelectFieldFormGUI
{

    /**
     * @var RadioField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, RadioField $object)
    {
        parent::__construct($parent, $object);
    }
}
