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
    const CMD_PARSE_EXCEL = "parseExcel";
    const CMD_CREATE_OR_UPDATE_USERS = "createOrUpdateUsers";
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

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_INPUT_EXCEL_IMPORT_DATA:
                    case self::CMD_PARSE_EXCEL:
                    case self::CMD_CREATE_OR_UPDATE_USERS:
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
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->ctrl()->saveParameter($this, Repository::GET_PARAM_REF_ID);

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", self::LANG_MODULE_EXCEL_IMPORT), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK_TO_MEMBERS_LIST));

        self::dic()->tabs()->addTab(self::TAB_EXCEL_IMPORT, self::plugin()->translate("title", self::LANG_MODULE_EXCEL_IMPORT), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_INPUT_EXCEL_IMPORT_DATA));
        self::dic()->tabs()->activateTab(self::TAB_EXCEL_IMPORT);
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
     * @return ExcelImport
     */
    protected function getExcelImport() : ExcelImport
    {
        $excel_import = new ExcelImport();

        return $excel_import;
    }


    /**
     *
     */
    protected function parseExcel()/*: void*/
    {
        $form = $this->getExcelImportForm();

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        $excel_import = $this->getExcelImport();

        $result = $excel_import->parse($form);
        if (empty($result)) {
            ilUtil::sendInfo(self::plugin()->translate("nothing_to_do", self::LANG_MODULE_EXCEL_IMPORT), true);

            self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);

            return;
        }
        ilUtil::sendInfo($result);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("create_or_update_users_confirmation", self::LANG_MODULE_EXCEL_IMPORT));

        $confirmation->setConfirm(self::plugin()->translate("create_or_update_users", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_CREATE_OR_UPDATE_USERS);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_BACK_TO_MEMBERS_LIST);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function createOrUpdateUsers()/*: void*/
    {
        $excel_import = $this->getExcelImport();

        $result = $excel_import->createOrUpdateUsers();
        ilUtil::sendSuccess($result, true);

        $result = $excel_import->getUsersToEnroll();
        if (empty($result)) {
            ilUtil::sendInfo(self::plugin()->translate("nothing_to_enroll", self::LANG_MODULE_EXCEL_IMPORT), true);

            self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);

            return;
        }
        ilUtil::sendInfo($result);

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("enroll_confirmation", self::LANG_MODULE_EXCEL_IMPORT));

        $confirmation->setConfirm(self::plugin()->translate("enroll", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_ENROLL);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE_EXCEL_IMPORT), self::CMD_BACK_TO_MEMBERS_LIST);

        self::output()->output($confirmation, true);
    }


    /**
     *
     */
    protected function enroll()/*: void*/
    {
        $excel_import = $this->getExcelImport();

        $result = $excel_import->enroll();

        ilUtil::sendSuccess($result, true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK_TO_MEMBERS_LIST);
    }


    /**
     *
     */
    protected function backToMembersList()/*: void*/
    {
        $excel_import = $this->getExcelImport();

        $excel_import->clean();

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
