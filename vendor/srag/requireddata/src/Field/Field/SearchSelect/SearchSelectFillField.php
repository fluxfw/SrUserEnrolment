<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\SearchSelect;

use ILIAS\UI\Component\Input\Field\Input;
use srag\RequiredData\SrUserEnrolment\Field\Field\MultiSearchSelect\MultiSearchSelectFillField;

/**
 * Class SearchSelectFillField
 *
 * @package srag\RequiredData\SrUserEnrolment\Field\Field\SearchSelect
 */
class SearchSelectFillField extends MultiSearchSelectFillField
{

    /**
     * @var SearchSelectField
     */
    protected $field;


    /**
     * @inheritDoc
     */
    public function __construct(SearchSelectField $field)
    {
        parent::__construct($field);
    }


    /**
     * @inheritDoc
     */
    public function getInput() : Input
    {
        $input = parent::getInput();

        $input->getInput()->setLimitCount(1);

        return $input;
    }
}
