<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect;

require_once __DIR__ . "/../../../../../../autoload.php";

use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\AbstractAjaxAutoCompleteCtrl;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class SMSSAjaxAutoCompleteCtrl
 *
 * @package           srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect
 *
 * @ilCtrl_isCalledBy srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect\SMSSAjaxAutoCompleteCtrl: srag\RequiredData\SrUserEnrolment\Field\FieldCtrl
 * @ilCtrl_isCalledBy srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect\SMSSAjaxAutoCompleteCtrl: srag\RequiredData\SrUserEnrolment\Field\Field\Group\GroupCtrl
 */
class SMSSAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    use RequiredDataTrait;

    /**
     * @var FieldCtrl
     */
    protected $parent;


    /**
     * SMSSAjaxAutoCompleteCtrl constructor
     *
     * @param FieldCtrl $parent
     */
    public function __construct(FieldCtrl $parent)
    {
        parent::__construct();

        $this->parent = $parent;
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        $form = self::requiredData()->fields()->factory()->newFormBuilderInstance($this->parent, $this->parent->getField());

        return $form->getAjaxAutoCompleteCtrl()->fillOptions($ids);
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(/*?*/ string $search = null) : array
    {
        $form = self::requiredData()->fields()->factory()->newFormBuilderInstance($this->parent, $this->parent->getField());

        return $form->getAjaxAutoCompleteCtrl()->searchOptions($search);
    }
}
