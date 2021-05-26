<?php

namespace srag\DataTableUI\SrUserEnrolment\Implementation\Data\Row;

use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;

/**
 * Class GetterRowData
 *
 * @package srag\DataTableUI\SrUserEnrolment\Implementation\Data\Row
 */
class GetterRowData extends AbstractRowData
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $key)
    {
        return Items::getter($this->getOriginalData(), $key);
    }
}
