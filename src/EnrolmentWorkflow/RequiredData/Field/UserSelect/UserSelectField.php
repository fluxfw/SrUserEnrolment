<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect;

use ilSrUserEnrolmentPlugin;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Field\Field\StaticMultiSearchSelect\StaticMultiSearchSelectField;

/**
 * Class UserSelectField
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\Field\UserSelect
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserSelectField extends StaticMultiSearchSelectField
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TABLE_NAME_SUFFIX = "usr";
}
