<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ExcelImportGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI: ilSrUserEnrolmentConfigGUI
 */
class ExcelImportGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_INPUT_EXCEL_IMPORT_DATA = "inputExcelImportData";
    const CMD_EXCEL_IMPORT = "excelImport";
    const CMD_ENROLL = "enroll";
    const CMD_BACK_TO_MEMBERS_LIST = "backToMembersList";
    const CMD_KEY_AUTOCOMPLETE = "keyAutoComplete";
    const TAB_EXCEL_IMPORT = "excel_import";
    const LANG_MODULE_EXCEL_IMPORT = "excel_import";


    /**
     * ExcelImportGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        if (!Config::getField(Config::KEY_SHOW_EXCEL_IMPORT) || !self::access()->currentUserHasRole()
            || !self::dic()->access()->checkAccess("write", "", self::rules()->getRefId())
        ) {
            die();
        }

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_INPUT_EXCEL_IMPORT_DATA:
                    case self::CMD_EXCEL_IMPORT:
                    case self::CMD_ENROLL:
                    case self::CMD_BACK_TO_MEMBERS_LIST:
                    case self::CMD_KEY_AUTOCOMPLETE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return ExcelImportFormGUI
     */
    protected function getExcelImportForm() : ExcelImportFormGUI
    {
        $form = new ExcelImportFormGUI($this);

        return $form;
    }


    /**
     *
     */
    protected function inputExcelImportData()/*: void*/
    {
        $form = $this->getExcelImportForm();

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function excelImport()/*: void*/
    {
        $form = $this->getExcelImportForm();

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        $excel_import = new ExcelImport();

        $result = $excel_import->import($form);

        self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);

        if (empty($result)) {

            ilUtil::sendInfo(self::plugin()->translate("nothing_to_enroll", self::LANG_MODULE_EXCEL_IMPORT), true);

            self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);

            return;
        }

        ilUtil::sendInfo($result, true);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("confirmation", self::LANG_MODULE_EXCEL_IMPORT));

        $confirmation->setConfirm(self::plugin()->translate("enroll", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_ENROLL);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_BACK_TO_MEMBERS_LIST);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function enroll()/*: void*/
    {
        $excel_import = new ExcelImport();

        $result = $excel_import->enroll();

        ilUtil::sendInfo($result, true);

        self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);
    }


    /**
     *
     */
    protected function backToMembersList()/*: void*/
    {
        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, Repository::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class,
            ilCourseMembershipGUI::class
        ]);
    }


    /**
     *
     */
    protected function keyAutoComplete()/*: void*/
    {
        $type = intval(filter_input(INPUT_GET, "type"));
        $term = strval(filter_input(INPUT_GET, "term"));

        $items = ExcelImport::getFieldsForType($type, $term);

        self::output()->outputJSON($items);
    }
}
