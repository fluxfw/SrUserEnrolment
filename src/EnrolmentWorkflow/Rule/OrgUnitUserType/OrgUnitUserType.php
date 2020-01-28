<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Operator\Operator;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\Position;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\RefId\RefId;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Title\Title;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use const srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position\POSITION_ALL;

/**
 * Class OrgUnitUserType
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\OrgUnitUserType
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitUserType extends AbstractRule
{

    use Operator;
    use Title;
    use RefId;
    use Position;
    const TABLE_NAME_SUFFIX = "orgusrtyp";
    const ORG_UNIT_USER_TYPE_TITLE = 1;
    const ORG_UNIT_USER_TYPE_TREE = 2;
    const ORG_UNIT_USER_TYPES
        = [
            self::ORG_UNIT_USER_TYPE_TITLE => "title",
            self::ORG_UNIT_USER_TYPE_TREE  => "tree"
        ];


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
            case null:
                return true;

            default:
                return false;
        }
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       2
     * @con_is_notnull   true
     */
    protected $org_unit_use_type = self::ORG_UNIT_USER_TYPE_TITLE;


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        $descriptions = [];

        switch ($this->org_unit_use_type) {
            case self::ORG_UNIT_USER_TYPE_TITLE:
                $descriptions[] = self::plugin()->translate("title", RulesGUI::LANG_MODULE) . " " . $this->getOperatorTitle() . " " . $this->title;
                break;

            case self::ORG_UNIT_USER_TYPE_TREE:
                $descriptions[] = self::plugin()->translate("ref_id", RulesGUI::LANG_MODULE) . " " . $this->getOperatorTitle() . " " . $this->ref_id;
                break;

            default:
                return "";
        }

        if ($this->getPosition() !== POSITION_ALL) {
            $descriptions[] = self::plugin()->translate("position", RulesGUI::LANG_MODULE) . " " . $this->getPositionTitle();
        }

        return nl2br(implode("\n", array_map(function (string $description) : string {
            return htmlspecialchars($description);
        }, $descriptions)), false);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        $field_value_operator = $this->sleepOperator($field_name, $field_value);
        if ($field_value_operator !== null) {
            return $field_value_operator;
        }

        switch ($field_name) {
            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        $field_value_operator = $this->wakeUpOperator($field_name, $field_value);
        if ($field_value_operator !== null) {
            return $field_value_operator;
        }

        switch ($field_name) {
            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }


    /**
     * @return int
     */
    public function getOrgUnitUserType() : int
    {
        return $this->org_unit_use_type;
    }


    /**
     * @param int $org_unit_user_type
     */
    public function setOrgUnitUserType(int $org_unit_user_type)/* : void*/
    {
        $this->org_unit_use_type = $org_unit_user_type;
    }
}
