<?php

namespace srag\CommentsUI\SrUserEnrolment\UI;

use srag\CommentsUI\SrUserEnrolment\Ctrl\CtrlInterface;
use srag\DIC\SrUserEnrolment\Plugin\Pluginable;

/**
 * Interface UIInterface
 *
 * @package srag\CommentsUI\SrUserEnrolment\UI
 */
interface UIInterface extends Pluginable
{

    const LANG_MODULE_COMMENTSUI = "commentsui";


    /**
     * @return string
     */
    public function render() : string;


    /**
     * @param CtrlInterface $ctrl_class
     *
     * @return self
     */
    public function withCtrlClass(CtrlInterface $ctrl_class) : self;


    /**
     * @param string $id
     *
     * @return self
     */
    public function withId(string $id) : self;
}
