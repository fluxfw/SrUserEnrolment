<?php

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\Local\ExcelImportLocalGUI;
use srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
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
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const PAR_SUB_TABS = "sub_tabs";
    const PAR_TABS = "tabs";
    const COURSE_MEMBER_LIST_TEMPLATE_ID = "Services/Table/tpl.table2.html";
    const TEMPLATE_GET = "template_get";
    const ACTIONS_MENU_TEMPLATE = "Services/UIComponent/AdvancedSelectionList/tpl.adv_selection_list.html";
    const COMPONENT_PERSONAL_DESKTOP = "Services/PersonalDesktop";
    const PART_CENTER_COLUMN = "center_column";
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_TARGET = "target";


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

        if ($a_comp === self::COMPONENT_PERSONAL_DESKTOP && $a_part === self::PART_CENTER_COLUMN) {

            return RequestInfoGUI::addRequestsToPersonalDesktop();
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
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

                AssistantsGUI::addTabs();
            }
        }

        if ($a_part === self::PAR_SUB_TABS) {

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseParticipantsGroupsGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilUsersGalleryGUI::class)
            ) {

                RulesCourseGUI::addTabs($this->getRefId());

                ExcelImportGUI::addTabs($this->getRefId());
            }

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLocalUserGUI::class)
                || (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjCategoryGUI::class)
                    && self::dic()->ctrl()->getCmd() === "listUsers")
            ) {

                ExcelImportLocalGUI::addTabs($this->getRefId());
            }

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjCourseGUI::class)) {

                SelectWorkflowGUI::addTabs($this->getRefId());

                RequestsGUI::addTabs($this->getRefId());
            }
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
