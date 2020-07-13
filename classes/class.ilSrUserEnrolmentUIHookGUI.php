<?php

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\DeputiesGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImportGUI;
use srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User\RulesUserGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ilSrUserEnrolmentUIHookGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentUIHookGUI extends ilUIHookPluginGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const ACTIONS_MENU_TEMPLATE = "Services/UIComponent/AdvancedSelectionList/tpl.adv_selection_list.html";
    const COMPONENT_DASHBOARD = "Services/Dashboard";
    const COMPONENT_PERSONAL_DESKTOP = "Services/PersonalDesktop";
    const COURSE_MEMBER_LIST_TEMPLATE_ID = "Services/Table/tpl.table2.html";
    const GET_PARAM_OBJ_ID = "obj_id";
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_TARGET = "target";
    const PART_CENTER_COLUMN = "center_column";
    const PART_RIGHT_COLUMN = "right_column";
    const PAR_SUB_TABS = "sub_tabs";
    const PAR_TABS = "tabs";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TEMPLATE_GET = "template_get";
    /**
     * @var bool
     */
    protected static $redirected = false;


    /**
     * ilSrUserEnrolmentUIHookGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getHTML(/*string*/ $a_comp, /*string*/ $a_part, $a_par = []) : array
    {
        if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
            || self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseParticipantsGroupsGUI::class)
            || self::dic()->ctrl()->getCmdClass() === strtolower(ilUsersGalleryGUI::class)
            || self::dic()->ctrl()->getCmdClass() === strtolower(ilMemberExportGUI::class)
        ) {

            if (!self::$redirected) {

                self::$redirected = true;

                if (self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess(self::dic()->user()->getId(), $this->getRefId())) {

                    $this->fixRedirect();

                    MembersGUI::redirect($this->getRefId());
                }
            }
        }

        if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
            && (empty(self::dic()->ctrl()->getCmd())
                || self::dic()->ctrl()->getCmd() === "participants")
        ) {

            if ($a_par["tpl_id"] === self::COURSE_MEMBER_LIST_TEMPLATE_ID && $a_part === self::TEMPLATE_GET) {

                return ResetPasswordGUI::addActions($a_par, $this->getRefId());
            }
        }

        if ($a_par["tpl_id"] === self::ACTIONS_MENU_TEMPLATE && $a_part === self::TEMPLATE_GET) {

            return RequestStepGUI::addObjectActions($a_par);
        }

        if (($a_comp === self::COMPONENT_DASHBOARD || $a_comp === self::COMPONENT_PERSONAL_DESKTOP) && $a_part === self::PART_CENTER_COLUMN) {

            return RequestInfoGUI::addRequestsToPersonalDesktop();
        }

        if ($a_comp === self::COMPONENT_PERSONAL_DESKTOP && $a_part === self::PART_RIGHT_COLUMN) {

            return [
                "mode" => self::PREPEND,
                "html" => AssistantsGUI::getAssistantsForPersonalDesktop(self::dic()->user()->getId()) . DeputiesGUI::getDeputiesForPersonalDesktop(self::dic()->user()->getId())
            ];
        }

        if (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjUserFolderGUI::class) && self::dic()->ctrl()->getCmd() === "importUserForm") {

            if (!self::$redirected) {

                self::$redirected = true;

                if (self::srUserEnrolment()->excelImport()->hasAccess(self::dic()->user()->getId(), $this->getRefId())) {
                    if (self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_EXCEL_IMPORT_USER_VIEW) === ConfigFormGUI::SHOW_EXCEL_IMPORT_USER_TYPE_REPLACE) {

                        switch (ExcelImportGUI::getObjType($this->getRefId())) {
                            case "crs":
                                $this->fixRedirect();

                                ExcelImportGUI::redirect($this->getRefId());
                                break;

                            case "cat":
                            case "orgu":
                            case "usrf":
                                $this->fixRedirect();

                                UserExcelImportGUI::redirect($this->getRefId());
                                break;

                            case "role":
                            default:
                                break;
                        }
                    }
                }
            }
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
    }


    /**
     * @inheritDoc
     */
    public function gotoHook()/*: void*/
    {
        $target = filter_input(INPUT_GET, "target");

        $matches = [];
        preg_match("/^uihk_" . ilSrUserEnrolmentPlugin::PLUGIN_ID . "_req(_(.*))?/uim", $target, $matches);

        if (is_array($matches) && count($matches) >= 1) {
            $this->fixRedirect();

            $request_id = explode("_", $matches[2]);
            if (isset($request_id[1])) {
                self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REF_ID, intval($request_id[1]));
            }
            $request_id = intval($request_id[0]);

            $request = self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequestById($request_id);

            self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REQUESTS_TYPE, RequestsGUI::REQUESTS_TYPE_OWN);
            self::dic()->ctrl()->setParameterByClass(RequestInfoGUI::class, RequestInfoGUI::GET_PARAM_REQUEST_ID, $request_id);

            if ($request !== null && $request->getUserId() === intval(self::dic()->user()->getId())) {
                self::dic()->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, RequestInfoGUI::class], RequestInfoGUI::CMD_SHOW_WORKFLOW);
            } else {
                self::dic()->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, RequestsGUI::class, RequestInfoGUI::class], RequestInfoGUI::CMD_SHOW_WORKFLOW);
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = [])/*: void*/
    {
        if ($a_part === self::PAR_TABS) {
            if (count(array_filter(self::dic()->ctrl()->getCallHistory(), function (array $history) : bool {
                    return (strtolower($history["class"]) === strtolower(ilPersonalProfileGUI::class));
                })) > 0
            ) {

                AssistantsGUI::addTabs(self::dic()->user()->getId());

                DeputiesGUI::addTabs(self::dic()->user()->getId());
            }

            if (count(array_filter(self::dic()->ctrl()->getCallHistory(), function (array $history) : bool {
                    return (strtolower($history["class"]) === strtolower(ilObjUserGUI::class));
                })) > 0
            ) {

                $user_id = intval(filter_input(INPUT_GET, "obj_id"));

                AssistantsGUI::addTabs($user_id);

                DeputiesGUI::addTabs($user_id);
            }
        }

        if ($a_part === self::PAR_SUB_TABS) {

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseParticipantsGroupsGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilUsersGalleryGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilMemberExportGUI::class)
            ) {

                RulesCourseGUI::addTabs($this->getRefId());

                ExcelImportGUI::addTabs($this->getRefId());
            }

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLocalUserGUI::class)
                || (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjCategoryGUI::class)
                    && self::dic()->ctrl()->getCmd() === "listUsers")
                || (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjUserFolderGUI::class) && self::dic()->ctrl()->getCmd() === "view")
            ) {

                UserExcelImportGUI::addTabs($this->getRefId());
            }

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjRoleGUI::class) && self::dic()->ctrl()->getCmd() === "userassignment") {

                RulesUserGUI::addTabs($this->getRefId(), $this->getObjId());

                UserExcelImportGUI::addTabs($this->getRefId(), $this->getObjId());
            }

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjCourseGUI::class)) {

                SelectWorkflowGUI::addTabs($this->getRefId());

                RequestsGUI::addTabs($this->getRefId());
            }
        }
    }


    /**
     *
     */
    protected function fixRedirect()/*: void*/
    {
        self::dic()->ctrl()->setTargetScript("ilias.php"); // Fix ILIAS 5.3 bug
        self::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class); // Fix ILIAS bug
    }


    /**
     * @return int|null
     */
    protected function getObjId()/*: ?int*/
    {
        $obj_id = filter_input(INPUT_GET, self::GET_PARAM_OBJ_ID);

        $obj_id = intval($obj_id);

        if ($obj_id > 0) {
            return $obj_id;
        } else {
            return null;
        }
    }


    /**
     * @return int|null
     */
    protected function getRefId()/*: ?int*/
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }
}
