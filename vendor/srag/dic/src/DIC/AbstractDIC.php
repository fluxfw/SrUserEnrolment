<?php

namespace srag\DIC\SrUserEnrolment\DIC;

use ILIAS\DI\Container;
use srag\DIC\SrUserEnrolment\Database\DatabaseDetector;
use srag\DIC\SrUserEnrolment\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrUserEnrolment\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
