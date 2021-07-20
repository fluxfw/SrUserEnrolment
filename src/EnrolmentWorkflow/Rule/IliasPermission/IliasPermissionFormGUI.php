<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission;

use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class IliasPermissionFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\IliasPermission
 */
class IliasPermissionFormGUI extends AbstractRuleFormGUI
{

    /**
     * @var IliasPermission
     */
    protected $rule;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, IliasPermission $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        parent::initFields();

        $this->fields = array_merge($this->fields, [
            "ilias_permission" => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => array_map(function (string $ilias_permission_lang_key) : array {
                    return [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("iliaspermission_" . $ilias_permission_lang_key)
                    ];
                }, IliasPermission::ILIAS_PERMISSIONS),
                "setTitle"              => $this->txt("rule_type_iliaspermission")
            ]
        ]);
    }
}
