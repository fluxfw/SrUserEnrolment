<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\Local;

use ilAdministrationGUI;
use ilLocalUserGUI;
use ilObjCategoryGUI;
use ilObjOrgUnitGUI;
use ilRepositoryGUI;
use ilUIPluginRouterGUI;
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

    const TAB_LOCAL_USER_ADMINISTRATION = "local_user_administration";


    /**
     * @inheritDoc
     */
    public static function addTabs(int $obj_ref_id)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->tabs()->addSubTab(self::TAB_LOCAL_USER_ADMINISTRATION, self::dic()->language()
                ->txt("administrate_users"), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_BACK));
            self::dic()->tabs()->activateSubTab(self::TAB_LOCAL_USER_ADMINISTRATION);

            self::dic()->tabs()->addSubTab(self::TAB_EXCEL_IMPORT, self::plugin()
                ->translate("title", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_INPUT_EXCEL_IMPORT_DATA));
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
                    ilObjCategoryGUI::class,
                ], "listUsers");
                break;

            case "orgu":
                self::dic()->ctrl()->saveParameterByClass(ilLocalUserGUI::class, self::GET_PARAM_REF_ID);

                self::dic()->ctrl()->redirectByClass([
                    ilAdministrationGUI::class,
                    ilObjOrgUnitGUI::class,
                    ilLocalUserGUI::class,
                ], "index");
                break;

            default:
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function getExcelImportForm() : ExcelImportFormGUI
    {
        $form = self::srUserEnrolment()->excelImport()->factory()->newLocalFormInstance($this);

        return $form;
    }


    /**
     * @return ExcelImport
     */
    protected function getExcelImport() : ExcelImport
    {
        $excel_import = self::srUserEnrolment()->excelImport()->factory()->newLocalImportInstance($this->obj_ref_id);

        return $excel_import;
    }
}
