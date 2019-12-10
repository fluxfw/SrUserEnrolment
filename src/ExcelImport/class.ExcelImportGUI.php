<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI;
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
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI: srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI
 */
class ExcelImportGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_CREATE_OR_UPDATE_USERS = "createOrUpdateUsers";
    const CMD_ENROLL = "enroll";
    const CMD_INPUT_EXCEL_IMPORT_DATA = "inputExcelImportData";
    const CMD_KEY_AUTO_COMPLETE = "keyAutoComplete";
    const CMD_PARSE_EXCEL = "parseExcel";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "excel_import";
    const TAB_EXCEL_IMPORT = "excel_import";
    /**
     * @var int
     */
    protected $obj_ref_id;


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
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));

        if (!self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(LogsGUI::class):
                self::dic()->ctrl()->forwardCommand(new LogsGUI($this->obj_ref_id));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_CREATE_OR_UPDATE_USERS:
                    case self::CMD_ENROLL:
                    case self::CMD_INPUT_EXCEL_IMPORT_DATA:
                    case self::CMD_KEY_AUTO_COMPLETE:
                    case self::CMD_PARSE_EXCEL:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->tabs()->addSubTab(self::TAB_EXCEL_IMPORT, self::plugin()
                ->translate("title", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_INPUT_EXCEL_IMPORT_DATA));
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_EXCEL_IMPORT, self::plugin()->translate("title", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_INPUT_EXCEL_IMPORT_DATA));
        self::dic()->tabs()->activateTab(self::TAB_EXCEL_IMPORT);

        LogsGUI::addTabs();
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        $excel_import = $this->getExcelImport();

        $excel_import->clean();

        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, self::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class,
            ilCourseMembershipGUI::class
        ]);
    }


    /**
     * @return ExcelImportFormGUI
     */
    protected function getExcelImportForm() : ExcelImportFormGUI
    {
        $form = self::srUserEnrolment()->excelImport()->factory()->newFormInstance($this);

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
        $excel_import = self::srUserEnrolment()->excelImport()->factory()->newImportInstance($this->obj_ref_id);

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

        $users = $excel_import->parse($form);
        if (empty($users)) {
            ilUtil::sendInfo(self::plugin()->translate("nothing_to_do", self::LANG_MODULE), true);

            self::dic()->ctrl()->redirect($this, self::CMD_BACK);

            return;
        }

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("create_or_update_users_confirmation", self::LANG_MODULE));

        $confirmation->setConfirm(self::plugin()->translate("create_or_update_users", self::LANG_MODULE), self::CMD_CREATE_OR_UPDATE_USERS);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_BACK);

        foreach ($users as $user_info) {
            $confirmation->addItem("", "", self::output()->getHTML($user_info));
        }

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

        $users = $excel_import->getUsersToEnroll();
        if (empty($users)) {
            ilUtil::sendInfo(self::plugin()->translate("nothing_to_enroll", self::LANG_MODULE), true);

            self::dic()->ctrl()->redirect($this, self::CMD_BACK);

            return;
        }

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("enroll_confirmation", self::LANG_MODULE));

        $confirmation->setConfirm(self::plugin()->translate("enroll", self::LANG_MODULE), self::CMD_ENROLL);
        $confirmation->setCancel(self::plugin()->translate("cancel", self::LANG_MODULE), self::CMD_BACK);

        foreach ($users as $user_info) {
            $confirmation->addItem("", "", self::output()->getHTML($user_info));
        }

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

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
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


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }
}
