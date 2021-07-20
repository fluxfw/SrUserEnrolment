<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers;

use ilCheckboxInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\ActionGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Field\FieldFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Value\ValueFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\UDF\UDF;

/**
 * Class AssignResponsibleUsersFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers
 */
class AssignResponsibleUsersFormGUI extends AbstractActionFormGUI
{

    use FieldFormGUI;
    use OperatorFormGUI;
    use ValueFormGUI;

    /**
     * @var AssignResponsibleUsers
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(ActionGUI $parent, AssignResponsibleUsers $action)
    {
        parent::__construct($parent, $action);
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        parent::initFields();

        $this->fields = array_merge(
            $this->fields,
            [
                "users_type" => [
                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => array_combine(array_keys(AssignResponsibleUsers::USER_TYPES), array_map(function (string $user_type_lang_key, int $user_type) : array {
                        switch ($user_type) {
                            case AssignResponsibleUsers::USER_TYPE_POSITION:
                                $items = [
                                    "assign_positions"           => [
                                        self::PROPERTY_CLASS    => MultiSelectSearchNewInputGUI::class,
                                        self::PROPERTY_REQUIRED => true,
                                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getPositions()
                                    ],
                                    "assign_positions_recursive" => [
                                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                                    ],
                                    "assign_positions_udf"       => [
                                        self::PROPERTY_CLASS    => MultiLineNewInputGUI::class,
                                        self::PROPERTY_SUBITEMS => array_merge(
                                            $this->getFieldFormFields(),
                                            $this->getOperatorFormFields1(),
                                            $this->getValueFormFields(),
                                            $this->getOperatorFormFields2()
                                        ),
                                        "setTitle"              => self::plugin()->translate("rule_type_" . UDF::getRuleType(), RulesGUI::LANG_MODULE)
                                    ]
                                ];
                                break;

                            case AssignResponsibleUsers::USER_TYPE_SPECIFIC_USERS:
                                $items = [
                                    "specific_users" => [
                                        self::PROPERTY_CLASS      => MultiSelectSearchNewInputGUI::class,
                                        self::PROPERTY_REQUIRED   => true,
                                        "setAjaxAutoCompleteCtrl" => new UsersAjaxAutoCompleteCtrl(),
                                        "setTitle"                => $this->txt("userstype_specific_users")
                                    ]
                                ];
                                break;

                            case AssignResponsibleUsers::USER_TYPE_GLOBAL_ROLES:
                                $items = [
                                    "global_role" => [
                                        self::PROPERTY_CLASS    => MultiSelectSearchNewInputGUI::class,
                                        self::PROPERTY_REQUIRED => true,
                                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getAllRoles(),
                                        "setTitle"              => self::plugin()->translate("rule_type_globalrole", RulesGUI::LANG_MODULE)
                                    ]
                                ];
                                break;

                            default:
                                $items = [];
                                break;
                        }

                        return [
                            self::PROPERTY_CLASS    => ilRadioOption::class,
                            self::PROPERTY_SUBITEMS => $items,
                            "setTitle"              => $this->txt("userstype_" . $user_type_lang_key)
                        ];
                    }, AssignResponsibleUsers::USER_TYPES, array_keys(AssignResponsibleUsers::USER_TYPES))),
                    "setTitle"              => $this->txt("userstype")
                ]
            ]);
    }
}
