<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Column\Formatter;

use srag\DataTableUI\SrUserEnrolment\Component\Column\Formatter\Formatter;
use srag\DataTableUI\SrUserEnrolment\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Column\Formatter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFormatter implements Formatter
{

    use DICTrait;
    use DataTableUITrait;


    /**
     * AbstractFormatter constructor
     */
    public function __construct()
    {

    }
}
