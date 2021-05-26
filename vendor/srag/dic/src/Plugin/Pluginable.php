<?php

namespace srag\DIC\SrUserEnrolment\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrUserEnrolment\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
