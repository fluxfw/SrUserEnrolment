<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ilAdministrationGUI;
use ilDatePresentation;
use ilObjUserGUI;
use ilPersonalDesktopGUI;
use ilSrUserEnrolmentPlugin;
use ilTemplate;
use ilUIPluginRouterGUI;
use ilUserAutoComplete;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class DeputiesGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\DeputiesGUI: ilUIPluginRouterGUI
 */
class DeputiesGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_EDIT_DEPUTIES = "editDeputies";
    const CMD_UPDATE_DEPUTIES = "updateDeputies";
    const CMD_USER_AUTOCOMPLETE = "userAutoComplete";
    const GET_PARAM_USER_ID = "user_id";
    const LANG_MODULE = "deputies";
    const TAB_EDIT_DEPUTIES = "edit_deputies";
    /**
     * @var int
     */
    protected $user_id;
    /**
     * @var array
     */
    protected $deputies;


    /**
     * DeputiesGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->user_id = intval(filter_input(INPUT_GET, self::GET_PARAM_USER_ID));

        if (!self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess($this->user_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_USER_ID);

        $this->deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputiesArray($this->user_id);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_EDIT_DEPUTIES:
                    case self::CMD_UPDATE_DEPUTIES:
                    case self::CMD_USER_AUTOCOMPLETE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $user_id
     *
     * @return string
     */
    public static function getDeputiesForPersonalDesktop(int $user_id) : string
    {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess($user_id)) {
            return "";
        }

        self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_USER_ID, $user_id);

        $tpl = self::plugin()->template("EnrolmentWorkflow/pd_deputies.html");
        $tpl->setVariable("TITLE", self::plugin()->translate("my_deputies", self::LANG_MODULE));
        $tpl->setVariable("EDIT_LINK", self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_DEPUTIES))));
        $deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputies($user_id);
        if (!empty($deputies)) {
            $tpl->setCurrentBlock("deputies");

            foreach (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputies($user_id) as $deputy) {
                $tpl->setVariable("USER", $deputy->getDeputyUser()->getFullname());
                if ($deputy->getUntil() !== null) {
                    $tpl_until = new ilTemplate(__DIR__ . "/../../../vendor/srag/custominputguis/src/PropertyFormGUI/Items/templates/input_gui_input_info.html", true, true);
                    $tpl_until->setVariable("INFO", self::plugin()->translate("until_date", self::LANG_MODULE, [
                        ilDatePresentation::formatDate($deputy->getUntil())
                    ]));
                    $tpl->setVariable("UNTIL", self::output()->getHTML($tpl_until));
                }
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setVariable("NO_ONE", self::plugin()->translate("nonone", self::LANG_MODULE));
        }

        $tpl2 = self::plugin()->template("EnrolmentWorkflow/pd_deputies.html");
        $tpl2->setVariable("TITLE", self::plugin()->translate("deputy_of", self::LANG_MODULE));
        $deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getDeputiesOf($user_id);
        if (!empty($deputies)) {
            foreach ($deputies as $deputy) {
                $tpl2->setCurrentBlock("deputies");

                $tpl2->setVariable("USER", $deputy->getUser()->getFullname());
                if ($deputy->getUntil() !== null) {
                    $tpl_until = new ilTemplate(__DIR__ . "/../../../vendor/srag/custominputguis/src/PropertyFormGUI/Items/templates/input_gui_input_info.html", true, true);
                    $tpl_until->setVariable("INFO", self::plugin()->translate("until_date", self::LANG_MODULE, [
                        ilDatePresentation::formatDate($deputy->getUntil())
                    ]));
                    $tpl2->setVariable("UNTIL", self::output()->getHTML($tpl_until));
                }
                $tpl2->parseCurrentBlock();
            }
        } else {
            $tpl2->setVariable("NO_ONE", self::plugin()->translate("nonone", self::LANG_MODULE));
        }

        return self::output()->getHTML([$tpl, $tpl2]);
    }


    /**
     * @param int $user_id
     */
    public static function addTabs(int $user_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess($user_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_USER_ID, $user_id);
            self::dic()
                ->tabs()
                ->addTab(self::TAB_EDIT_DEPUTIES, self::plugin()->translate(($user_id === intval(self::dic()->user()->getId()) ? "my_" : "") . "deputies", self::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_DEPUTIES));
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()
            ->tabs()
            ->addTab(self::TAB_EDIT_DEPUTIES, self::plugin()->translate(($this->user_id === intval($user_id) ? "my_" : "") . "deputies", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass(self::class, self::CMD_EDIT_DEPUTIES));
    }


    /**
     *
     */
    protected function back()/*:void*/
    {
        if ($this->user_id === intval(self::dic()->user()->getId())) {
            self::dic()->ctrl()->redirectByClass(ilPersonalDesktopGUI::class, "jumpToProfile");
        } else {
            self::dic()->ctrl()->setParameterByClass(ilObjUserGUI::class, "obj_id", $this->user_id);
            self::dic()->ctrl()->redirectByClass([ilAdministrationGUI::class, ilObjUserGUI::class], "view");
        }
    }


    /**
     *
     */
    protected function editDeputies()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_DEPUTIES);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->factory()->newFormInstance($this, $this->deputies);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateDeputies()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_DEPUTIES);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->factory()->newFormInstance($this, $this->deputies);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        $this->deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->storeUserDeputiesArray($this->user_id, $form->getDeputies());

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_DEPUTIES);
    }


    /**
     *
     */
    protected function userAutoComplete()/*:void*/
    {
        $auto = new ilUserAutoComplete();
        $auto->setSearchFields(["login", "firstname", "lastname", "email", "usr_id"]);
        $auto->setMoreLinkAvailable(true);
        $auto->setResultField("usr_id");

        if (filter_input(INPUT_GET, "fetchall")) {
            $auto->setLimit(ilUserAutoComplete::MAX_ENTRIES);
        }

        // TODO: Skip self

        echo $auto->getList(filter_input(INPUT_GET, "term"));

        exit;
    }


    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }
}
