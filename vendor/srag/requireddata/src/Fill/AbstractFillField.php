<?php

namespace srag\RequiredData\SrUserEnrolment\Fill;

use ILIAS\UI\Component\Input\Field\Input;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\RequiredData\SrUserEnrolment\Field\AbstractField;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class AbstractFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Fill
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFillField
{

    use DICTrait;
    use RequiredDataTrait;

    /**
     * @var AbstractField
     */
    protected $field;


    /**
     * AbstractFillField constructor
     *
     * @param AbstractField $field
     */
    public function __construct(AbstractField $field)
    {
        $this->field = $field;
    }


    /**
     * @return Input
     */
    public abstract function getInput() : Input;


    /**
     * @param mixed $fill_value
     *
     * @return mixed
     */
    public abstract function formatAsJson($fill_value);


    /**
     * @param mixed $fill_value
     *
     * @return string
     */
    public abstract function formatAsString($fill_value) : string;
}
