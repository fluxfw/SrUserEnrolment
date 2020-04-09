<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\Local;

use ilAdministrationGUI;
use ilLocalUserGUI;
use ilObjCategoryGUI;
use ilObjOrgUnitGUI;
use ilObjUserFolderGUI;
use ilRepositoryGUI;
use ilUIPluginRouterGUI;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;

/**
 * Class ExcelImportLocalGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ExcelImport\Local
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImport\Local\ExcelImportLocalGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI: srag\Plugins\SrUserEnrolment\ExcelImport\Local\ExcelImportLocalGUI
 */
class ExcelImportLocalGUI extends ExcelImportGUI
{

    /**
     * @inheritDoc
     */
    public static function addTabs(int $obj_ref_id)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_LOCAL_TYPE) === ConfigFormGUI::SHOW_EXCEL_IMPORT_LOCAL_TYPE_SEPARATE) {
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

                self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::getTitle(), self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    self::class
                ], self::CMD_INPUT_EXCEL_IMPORT_DATA)));
            }
        }
    }


    /**
     * @inheritDoc
     */
    public static function redirect(int $obj_ref_id)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_LOCAL_TYPE) === ConfigFormGUI::SHOW_EXCEL_IMPORT_LOCAL_TYPE_REPLACE) {
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

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
        if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_LOCAL_TYPE) === ConfigFormGUI::SHOW_EXCEL_IMPORT_LOCAL_TYPE_REPLACE) {
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
        switch (self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))) {
            case "usrf":
                return self::dic()->language()->txt("obj_usrf");
                break;

            case "cat":
            case "orgu":
            default:
                return parent::getBackTitle();
        }
    }


    /**
     * @inheritDoc
     */
    protected function back()/*: void*/
    {
        switch (self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))) {
            case "cat":
                self::dic()->ctrl()->saveParameterByClass(ilObjCategoryGUI::class, self::GET_PARAM_REF_ID);

                self::dic()->ctrl()->redirectByClass([
                    ilRepositoryGUI::class,
                    ilObjCategoryGUI::class
                ], "listUsers");
                break;

            case "orgu":
                self::dic()->ctrl()->saveParameterByClass(ilLocalUserGUI::class, self::GET_PARAM_REF_ID);

                self::dic()->ctrl()->redirectByClass([
                    ilAdministrationGUI::class,
                    ilObjOrgUnitGUI::class,
                    ilLocalUserGUI::class
                ], "index");
                break;

            case "usrf":
                self::dic()->ctrl()->saveParameterByClass(ilObjUserFolderGUI::class, self::GET_PARAM_REF_ID);

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
        $form = self::srUserEnrolment()->excelImport()->factory()->newLocalFormInstance($this);

        return $form;
    }


    /**
     * @return ExcelImport
     */
    protected function newImportInstance() : ExcelImport
    {
        $excel_import = self::srUserEnrolment()->excelImport()->factory()->newLocalImportInstance($this->obj_ref_id);

        return $excel_import;
    }
}
