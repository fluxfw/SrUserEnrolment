<?php

namespace srag\CommentsUI\SrUserEnrolment\UI;

use srag\CommentsUI\SrUserEnrolment\Ctrl\CtrlInterface;

/**
 * Interface UIInterface
 *
 * @package srag\CommentsUI\SrUserEnrolment\UI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface UIInterface
{

    const LANG_MODULE_COMMENTSUI = "commentsui";


    /**
     * @param string $id
     *
     * @return self
     */
    public function withId(string $id) : self;


    /**
     * @param CtrlInterface $ctrl_class
     *
     * @return self
     */
    public function withCtrlClass(CtrlInterface $ctrl_class) : self;


    /**
     * @return string
     */
    public function render() : string;
}
