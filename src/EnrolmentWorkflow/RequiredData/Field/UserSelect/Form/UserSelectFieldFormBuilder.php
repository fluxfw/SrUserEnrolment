<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect\Form;

use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\AbstractAjaxAutoCompleteCtrl;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect\UserSelectField;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect\Form\StaticMultiSearchSelectFieldFormBuilder;
use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class UserSelectFieldFormBuilder
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect\Form
 */
class UserSelectFieldFormBuilder extends StaticMultiSearchSelectFieldFormBuilder
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var UserSelectField
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(FieldCtrl $parent, UserSelectField $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    public function getAjaxAutoCompleteCtrl() : AbstractAjaxAutoCompleteCtrl
    {
        return new UsersAjaxAutoCompleteCtrl();
    }
}
