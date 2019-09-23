<?php

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Rule\RulesGUI;
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
    const COURSE_MEMBER_LIST_TEMPLATE_ID = "Services/Table/tpl.table2.html";
    const TEMPLATE_GET = "template_get";


    /**
     * ilSrUserEnrolmentUIHookGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param string $a_comp
     * @param string $a_part
     * @param array  $a_par
     *
     * @return array
     */
    public function getHTML(/*string*/ $a_comp, /*string*/ $a_part, $a_par = []) : array
    {
        if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
            && (empty(self::dic()->ctrl()->getCmd())
                || self::dic()->ctrl()->getCmd() === "participants")
        ) {

            if ($a_par["tpl_id"] === self::COURSE_MEMBER_LIST_TEMPLATE_ID && $a_part === self::TEMPLATE_GET) {

                if (Config::getField(Config::KEY_SHOW_RESET_PASSWORD)) {

                    if (self::access()->currentUserHasRole()) {

                        $html = $a_par["html"];

                        $course = new ilObjCourse(self::rules()->getRefId());

                        $html = preg_replace_callback('/<a class="il_ContainerItemCommand2" href=".+member_id=([0-9]+).+cmd=editMember.+">.+<\/a>/', function (array $matches) use ($course): string {
                            $link = $matches[0];

                            $user_id = intval($matches[1]);

                            if (self::ilias()->courses()->isMember($course, $user_id)) {
                                self::dic()->ctrl()->saveParameterByClass(ResetPasswordGUI::class, Repository::GET_PARAM_REF_ID);

                                self::dic()->ctrl()->setParameterByClass(ResetPasswordGUI::class, Repository::GET_PARAM_USER_ID, $user_id);

                                $reset_password_link = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::plugin()
                                    ->translate("button", ResetPasswordGUI::LANG_MODULE_RESET_PASSWORD), self::dic()->ctrl()->getLinkTargetByClass([
                                    ilUIPluginRouterGUI::class,
                                    ResetPasswordGUI::class
                                ], ResetPasswordGUI::CMD_RESET_PASSWORD_CONFIRM)));

                                $reset_password_link = str_replace('<a ', '<a class="il_ContainerItemCommand2" ', $reset_password_link);

                                return self::output()->getHTML([
                                    $link,
                                    "<br>",
                                    $reset_password_link
                                ]);
                            } else {
                                return $link;
                            }
                        }, $html);

                        return ["mode" => self::REPLACE, "html" => $html];
                    }
                }
            }
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
    }


    /**
     * @param string $a_comp
     * @param string $a_part
     * @param array  $a_par
     */
    public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = [])/*: void*/
    {

        if ($a_part === self::PAR_SUB_TABS) {

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseMembershipGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilCourseParticipantsGroupsGUI::class)
                || self::dic()->ctrl()->getCmdClass() === strtolower(ilUsersGalleryGUI::class)
            ) {

                if (self::access()->currentUserHasRole()) {

                    self::dic()->ctrl()->setParameterByClass(RulesGUI::class, Repository::GET_PARAM_REF_ID, self::rules()->getRefId());
                    self::dic()->tabs()->addSubTab(RulesGUI::TAB_RULES, self::plugin()->translate("rules", RulesGUI::LANG_MODULE_RULES), self::dic()
                        ->ctrl()->getLinkTargetByClass([
                            ilUIPluginRouterGUI::class,
                            RulesGUI::class
                        ], RulesGUI::CMD_LIST_RULES));

                    if (Config::getField(Config::KEY_SHOW_EXCEL_IMPORT)) {
                        self::dic()->ctrl()->setParameterByClass(ExcelImportGUI::class, Repository::GET_PARAM_REF_ID, self::rules()->getRefId());
                        self::dic()->tabs()->addSubTab(ExcelImportGUI::TAB_EXCEL_IMPORT, self::plugin()
                            ->translate("title", ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT), self::dic()->ctrl()->getLinkTargetByClass([
                            ilUIPluginRouterGUI::class,
                            ExcelImportGUI::class
                        ], ExcelImportGUI::CMD_INPUT_EXCEL_IMPORT_DATA));
                    }
                }
            }
        }
    }
}
