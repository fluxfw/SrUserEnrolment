<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilConfirmationGUI;
use ilCourseMembershipGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
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

    const CMD_BACK = "back";
    const CMD_CREATE_OR_UPDATE_USERS = "createOrUpdateUsers";
    const CMD_ENROLL = "enroll";
    //const CMD_CREATE_OR_UPDATE_USERS_CONFIRMATION = "createOrUpdateUsersConfirmation";
    const CMD_ENROLL_CONFIRMATION = "enrollConfirmation";
    const CMD_INPUT_EXCEL_IMPORT_DATA = "inputExcelImportData";
    const CMD_KEY_AUTO_COMPLETE = "keyAutoComplete";
    const CMD_PARSE_EXCEL = "parseExcel";
    const GET_PARAM_OBJ_SINGLE_ID = "obj_single_id";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "excel_import";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_EXCEL_IMPORT = "excel_import";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var int|null
     */
    protected $obj_single_id = null;


    /**
     * ExcelImportGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     */
    public static function addTabs(int $obj_ref_id,/*?*/ int $obj_single_id = null)/*:void*/
    {
        if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $obj_ref_id, $obj_single_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_OBJ_SINGLE_ID, $obj_single_id);

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(static::getTitle(), str_replace("\\", "\\\\", self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_INPUT_EXCEL_IMPORT_DATA))));
        }
    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     *
     * @return int
     */
    public static function getObjId(int $obj_ref_id,/*?*/ int $obj_single_id = null) : int
    {
        if (!empty($obj_single_id)) {
            return $obj_single_id;
        } else {
            return self::dic()->objDataCache()->lookupObjId($obj_ref_id);
        }
    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     *
     * @return string
     */
    public static function getObjType(int $obj_ref_id,/*?*/ int $obj_single_id = null) : string
    {
        return self::dic()->objDataCache()->lookupType(static::getObjId($obj_ref_id, $obj_single_id));
    }


    /**
     * @return string
     */
    public static function getTitle() : string
    {
        return self::plugin()->translate("title", self::LANG_MODULE);
    }


    /**
     * @param int      $obj_ref_id
     * @param int|null $obj_single_id
     */
    public static function redirect(int $obj_ref_id,/*?*/ int $obj_single_id = null)/*: void*/
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
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));
        $this->obj_single_id = (intval(filter_input(INPUT_GET, self::GET_PARAM_OBJ_SINGLE_ID)) ?? null);

        if (!self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id, $this->obj_single_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_OBJ_SINGLE_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(LogsGUI::class):
                self::dic()->ctrl()->forwardCommand(new LogsGUI(static::getObjId($this->obj_ref_id, $this->obj_single_id)));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_CREATE_OR_UPDATE_USERS:
                        //case self::CMD_CREATE_OR_UPDATE_USERS_CONFIRMATION:
                    case self::CMD_ENROLL:
                    case self::CMD_ENROLL_CONFIRMATION:
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
     * @return string
     */
    public function getBackTitle() : string
    {
        return self::dic()->objDataCache()->lookupTitle(static::getObjId($this->obj_ref_id, $this->obj_single_id));
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @return int|null
     */
    public function getObjSingleId()/* : ?int*/
    {
        return $this->obj_single_id;
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        $excel_import = $this->newImportInstance();

        $excel_import->clean();

        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, self::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class,
            ilCourseMembershipGUI::class
        ]);
    }


    /**
     *
     */
    protected function createOrUpdateUsers()/*: void*/
    {
        $excel_import = $this->newImportInstance();

        $result = $excel_import->createOrUpdateUsers();
        ilUtil::sendSuccess($result, true);

        self::dic()->ctrl()->redirect($this, self::CMD_ENROLL_CONFIRMATION);
    }


    /**
     * @param array $users
     */
    protected function createOrUpdateUsersConfirmation(array $users)/*: void*/
    {
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
    protected function enroll()/*: void*/
    {
        $excel_import = $this->newImportInstance();

        $result = $excel_import->enroll();

        ilUtil::sendSuccess($result, true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function enrollConfirmation()/*: void*/
    {
        $excel_import = $this->newImportInstance();

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
    protected function inputExcelImportData()/*: void*/
    {
        $form = $this->newFormInstance();

        self::output()->output($form, true);
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
     * @return ExcelImportFormGUI
     */
    protected function newFormInstance() : ExcelImportFormGUI
    {
        $form = self::srUserEnrolment()->excelImport()->factory()->newFormInstance($this);

        return $form;
    }


    /**
     * @return ExcelImport
     */
    protected function newImportInstance() : ExcelImport
    {
        $excel_import = self::srUserEnrolment()->excelImport()->factory()->newImportInstance($this);

        return $excel_import;
    }


    /**
     *
     */
    protected function parseExcel()/*: void*/
    {
        $form = $this->newFormInstance();

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        $excel_import = $this->newImportInstance();

        $users = $excel_import->parse($form);

        if ($users === null) {
            self::output()->output($form, true);

            return;
        }

        if (empty(array_filter($users))) {
            self::dic()->ctrl()->redirect($this, self::CMD_ENROLL_CONFIRMATION);

            return;
        }

        //self::dic()->ctrl()->redirect($this, self::CMD_CREATE_OR_UPDATE_USERS_CONFIRMATION);
        $this->createOrUpdateUsersConfirmation($users);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->setBackTarget($this->getBackTitle(), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_EXCEL_IMPORT, static::getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_INPUT_EXCEL_IMPORT_DATA));
        self::dic()->tabs()->activateTab(self::TAB_EXCEL_IMPORT);

        LogsGUI::addTabs();
    }
}
