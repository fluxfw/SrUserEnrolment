<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User;

require_once __DIR__ . "/../../../../vendor/autoload.php";

use ilAdministrationGUI;
use ilObjectGUIFactory;
use ilObjRoleFolderGUI;
use ilObjRoleGUI;
use ilPermissionGUI;
use ilSrUserEnrolmentUIHookGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;

/**
 * Class RulesUserGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User\RulesUserGUI: ilUIPluginRouterGUI
 */
class RulesUserGUI extends RulesCourseGUI
{

    /**
     * @inheritDoc
     */
    public static function getTitle() : string
    {
        return self::plugin()->translate("type_role_rule", RulesGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function getRuleContext() : int
    {
        return AbstractRule::PARENT_CONTEXT_ROLE;
    }


    /**
     * @inheritDoc
     */
    public function getRuleType() : int
    {
        return AbstractRule::TYPE_ROLE_RULE;
    }


    /**
     * @inheritDoc
     */
    protected function back()/*: void*/
    {
        switch (static::getObjType($this->obj_ref_id, $this->obj_single_id)) {
            case "role":
                $parent_gui = get_class((new ilObjectGUIFactory())->getInstanceByRefId($this->obj_ref_id));

                self::dic()->ctrl()->setParameterByClass(ilAdministrationGUI::class, ilSrUserEnrolmentUIHookGUI::GET_PARAM_REF_ID, $this->obj_ref_id);
                self::dic()->ctrl()->setParameterByClass(ilAdministrationGUI::class, ilSrUserEnrolmentUIHookGUI::GET_PARAM_OBJ_ID, $this->obj_single_id);

                if ($parent_gui !== ilObjRoleFolderGUI::class) {
                    self::dic()->ctrl()->redirectByClass([
                        ilAdministrationGUI::class,
                        $parent_gui,
                        ilPermissionGUI::class,
                        ilObjRoleGUI::class
                    ], "userassignment");
                } else {
                    self::dic()->ctrl()->redirectByClass([
                        ilAdministrationGUI::class,
                        ilObjRoleGUI::class
                    ], "userassignment");
                }
                break;

            default:
                break;
        }
    }
}
