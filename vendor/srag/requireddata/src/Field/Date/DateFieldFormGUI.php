<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Date;

use srag\RequiredData\SrUserEnrolment\Field\AbstractFieldFormGUI;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class DateFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Date
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DateFieldFormGUI extends AbstractFieldFormGUI
{

    /**
     * @var DateField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, DateField $object)
    {
        parent::__construct($parent, $object);
    }
}
