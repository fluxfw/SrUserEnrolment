<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImportLocal;

use ilAdministrationGUI;
use ilLocalUserGUI;
use ilObjCategoryGUI;
use ilObjOrgUnitGUI;
use ilRepositoryGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\Rule\Repository;

/**
 * Class ExcelImportLocalGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ExcelImportLocal
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImportLocal\ExcelImportLocalGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImportLocal\ExcelImportLocalGUI: ilSrUserEnrolmentConfigGUI
 */
class ExcelImportLocalGUI extends ExcelImportGUI
{

    const TAB_LOCAL_USER_ADMINISTRATION = "local_user_administration";


    /**
     * @inheritDoc
     */
    protected function getExcelImportForm() : ExcelImportFormGUI
    {
        $form = new ExcelImportLocalFormGUI($this);

        return $form;
    }


    /**
     * @return ExcelImport
     */
    protected function getExcelImport() : ExcelImport
    {
        $excel_import = new ExcelImportLocal();

        return $excel_import;
    }


    /**
     * @inheritDoc
     */
    protected function backToMembersList()/*: void*/
    {
        switch ($a = self::dic()->objDataCache()->lookupType(self::rules()->getObjId())) {
            case "cat":
                self::dic()->ctrl()->saveParameterByClass(ilObjCategoryGUI::class, Repository::GET_PARAM_REF_ID);

                self::dic()->ctrl()->redirectByClass([
                    ilRepositoryGUI::class,
                    ilObjCategoryGUI::class,
                ], "listUsers");
                break;

            case "orgu":
                self::dic()->ctrl()->saveParameterByClass(ilLocalUserGUI::class, Repository::GET_PARAM_REF_ID);

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
}
