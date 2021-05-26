<?php

namespace srag\Notifications4Plugin\SrUserEnrolment\Parser;

/**
 * Interface FactoryInterface
 *
 * @package srag\Notifications4Plugin\SrUserEnrolment\Parser
 */
interface FactoryInterface
{

    /**
     * @return twigParser
     */
    public function twig() : twigParser;
}
