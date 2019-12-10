<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType;

use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRuleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\OperatorFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\PositionFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId\RefIdFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title\TitleFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RuleGUI;

/**
 * Class OrgUnitUserTypeFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitUserTypeFormGUI extends AbstractRuleFormGUI
{

    use OperatorFormGUI;
    use TitleFormGUI;
    use RefIdFormGUI;
    use PositionFormGUI;
    /**
     * @var OrgUnitUserType
     */
    protected $object;


    /**
     * @inheritDoc
     */
    public function __construct(RuleGUI $parent, OrgUnitUserType $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "operator_subsequent":
                return parent::getValue("operator");

            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        parent::initFields();

        $sub_fields = [
            array_merge(
                $this->getOperatorFormFields1(),
                $this->getOperatorFormFields2(),
                $this->getTitleFormFields()
            ),
            array_merge(
                $this->getOperatorFormFieldsSubsequent1(),
                $this->getRefIdFormFields()
            )
        ];

        $this->fields = array_merge(
            $this->fields,
            [
                "org_unit_user_type" => [
                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    self::PROPERTY_SUBITEMS => array_map(function (string $org_unit_user_type_lang_key) use (&$sub_fields): array {
                        return [
                            self::PROPERTY_CLASS    => ilRadioOption::class,
                            self::PROPERTY_SUBITEMS => array_shift($sub_fields),
                            "setTitle"              => $this->txt("orgunitusertype_" . $org_unit_user_type_lang_key)
                        ];
                    }, OrgUnitUserType::ORG_UNIT_USER_TYPES),
                    "setTitle"              => $this->txt("rule_type_orgunitusertype")
                ]
            ],
            $this->getPositionFormFields(false)
        );
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "operator_subsequent":
                switch ($this->object->getOrgUnitUserType()) {
                    case OrgUnitUserType::ORG_UNIT_USER_TYPE_TREE:
                        parent::storeValue("operator", $value);
                        break;

                    default:
                        break;
                }
                break;

            default:
                parent::storeValue($key, $value);
                break;
        }
    }
}
