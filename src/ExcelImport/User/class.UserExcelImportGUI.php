<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\User;

use ilAdministrationGUI;
use ilLocalUserGUI;
use ilObjCategoryGUI;
use ilObjectGUIFactory;
use ilObjOrgUnitGUI;
use ilObjRoleFolderGUI;
use ilObjRoleGUI;
use ilObjUserFolderGUI;
use ilPermissionGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentUIHookGUI;
use ilUIPluginRouterGUI;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;

/**
 * Class UserExcelImportGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ExcelImport\User
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImportGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\Log\LogsGUI: srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImportGUI
 */
class UserExcelImportGUI extends ExcelImportGUI
{

    /**
     * @inheritDoc
     */
    public static function addTabs(int $obj_ref_id,/*?*/ int $obj_single_id = null)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $obj_single_id)) {
            if (static::getObjType($obj_ref_id, $obj_single_id) === "role"
                || self::srUserEnrolment()
                    ->config()
                    ->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER_VIEW) === ConfigFormGUI::SHOW_EXCEL_IMPORT_USER_TYPE_SEPARATE
            ) {
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_OBJ_SINGLE_ID, $obj_single_id);

                self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::getTitle(), str_replace("\\", "\\\\", self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    self::class
                ], self::CMD_INPUT_EXCEL_IMPORT_DATA))));
            }
        }
    }


    /**
     * @inheritDoc
     */
    public static function redirect(int $obj_ref_id,/*?*/ int $obj_single_id = null)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $obj_single_id)) {
            if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER_VIEW) === ConfigFormGUI::SHOW_EXCEL_IMPORT_USER_TYPE_REPLACE) {
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_OBJ_SINGLE_ID, $obj_single_id);

                self::dic()->ctrl()->redirectByClass([
                    ilUIPluginRouterGUI::class,
                    self::class
                ], self::CMD_INPUT_EXCEL_IMPORT_DATA);
            }
        }
    }


    /**
     * @inheritDoc
     */
    public static function getTitle() : string
    {
        if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER_VIEW) === ConfigFormGUI::SHOW_EXCEL_IMPORT_USER_TYPE_REPLACE
        ) {
            return self::dic()->language()->txt("import_users");
        } else {
            return parent::getTitle();
        }
    }


    /**
     * @inheritDoc
     */
    public function getBackTitle() : string
    {
        switch (static::getObjType($this->obj_ref_id, $this->obj_single_id)) {
            case "usrf":
                return self::dic()->language()->txt("obj_usrf");

            case "cat":
            case "orgu":
            case "role":
            default:
                return parent::getBackTitle();
        }
    }


    /**
     * @inheritDoc
     */
    protected function back()/*: void*/
    {
        switch (static::getObjType($this->obj_ref_id, $this->obj_single_id)) {
            case "cat":
                self::dic()->ctrl()->setParameterByClass(ilRepositoryGUI::class, ilSrUserEnrolmentUIHookGUI::GET_PARAM_REF_ID, $this->obj_ref_id);

                self::dic()->ctrl()->redirectByClass([
                    ilRepositoryGUI::class,
                    ilObjCategoryGUI::class
                ], "listUsers");
                break;

            case "orgu":
                self::dic()->ctrl()->setParameterByClass(ilAdministrationGUI::class, ilSrUserEnrolmentUIHookGUI::GET_PARAM_REF_ID, $this->obj_ref_id);

                self::dic()->ctrl()->redirectByClass([
                    ilAdministrationGUI::class,
                    ilObjOrgUnitGUI::class,
                    ilLocalUserGUI::class
                ], "index");
                break;

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

            case "usrf":
                self::dic()->ctrl()->setParameterByClass(ilAdministrationGUI::class, ilSrUserEnrolmentUIHookGUI::GET_PARAM_REF_ID, $this->obj_ref_id);

                self::dic()->ctrl()->redirectByClass([
                    ilAdministrationGUI::class,
                    ilObjUserFolderGUI::class
                ], "view");
                break;

            default:
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function newFormInstance() : ExcelImportFormGUI
    {
        $form = self::srUserEnrolment()->excelImport()->factory()->newUserFormInstance($this);

        return $form;
    }


    /**
     * @return ExcelImport
     */
    protected function newImportInstance() : ExcelImport
    {
        $excel_import = self::srUserEnrolment()->excelImport()->factory()->newUserImportInstance($this);

        return $excel_import;
    }
}
