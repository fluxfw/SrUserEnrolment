<?php

namespace srag\Plugins\SrUserEnrolment\Exception;

use ilException;

/**
 * Class SrUserEnrolmentException
 *
 * @package srag\Plugins\SrUserEnrolment\Exception
 */
class SrUserEnrolmentException extends ilException
{

    /**
     * SrUserEnrolmentException constructor
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
