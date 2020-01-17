<?php

namespace srag\CustomInputGUIs\SrUserEnrolment\NumberInputGUI;

use ilNumberInputGUI;
use ilTableFilterItem;
use ilToolbarItem;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class NumberInputGUI
 *
 * @package srag\CustomInputGUIs\SrUserEnrolment\NumberInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NumberInputGUI extends ilNumberInputGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()/*: string*/
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML()/*: string*/
    {
        return $this->render();
    }
}
