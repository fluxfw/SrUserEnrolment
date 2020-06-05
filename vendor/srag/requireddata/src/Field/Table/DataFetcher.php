<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Table;

use srag\DataTableUI\SrUserEnrolment\Component\Data\Data;
use srag\DataTableUI\SrUserEnrolment\Component\Data\Row\RowData;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Settings;
use srag\DataTableUI\SrUserEnrolment\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\RequiredData\SrUserEnrolment\Field\AbstractField;
use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class DataFetcher
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DataFetcher extends AbstractDataFetcher
{

    use RequiredDataTrait;

    /**
     * @var FieldsCtrl
     */
    protected $parent;


    /**
     * @inheritDoc
     */
    public function __construct(FieldsCtrl $parent)
    {
        $this->parent = $parent;

        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function fetchData(Settings $settings) : Data
    {
        $data = self::requiredData()->fields()->getFields($this->parent->getParentContext(), $this->parent->getParentId(), null, false);

        return self::dataTableUI()->data()->data(array_map(function (AbstractField $field
        ) : RowData {
            return self::dataTableUI()->data()->row()->getter($field->getId(), $field);
        }, $data), count($data));
    }
}
