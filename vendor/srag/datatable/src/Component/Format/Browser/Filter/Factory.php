<?php

namespace srag\DataTableUI\SrUserEnrolment\Component\Format\Browser\Filter;

use srag\CustomInputGUIs\SrUserEnrolment\FormBuilder\FormBuilder;
use srag\DataTableUI\SrUserEnrolment\Component\Format\Browser\BrowserFormat;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Settings;
use srag\DataTableUI\SrUserEnrolment\Component\Table;

/**
 * Interface Factory
 *
 * @package srag\DataTableUI\SrUserEnrolment\Component\Format\Browser\Filter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @param BrowserFormat $parent
     * @param Table         $component
     * @param Settings      $settings
     *
     * @return FormBuilder
     */
    public function formBuilder(BrowserFormat $parent, Table $component, Settings $settings) : FormBuilder;
}
