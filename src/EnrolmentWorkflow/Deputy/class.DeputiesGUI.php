<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ilDatePresentation;
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
    const LANG_MODULE = "deputies";
    const TAB_EDIT_DEPUTIES = "edit_deputies";
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
        $this->deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputiesArray(self::dic()->user()->getId());

        if (!self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId())) {
            die();
        }

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
     * @return string
     */
    public static function getDeputiesForPersonalDesktop() : string
    {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId())) {
            return "";
        }

        $tpl = self::plugin()->template("EnrolmentWorkflow/pd_deputies.html");
        $tpl->setVariable("TITLE", self::plugin()->translate("my_deputies", self::LANG_MODULE));
        $tpl->setVariable("EDIT_LINK", self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_DEPUTIES))));
        $deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputies(self::dic()->user()->getId());
        if (!empty($deputies)) {
            $tpl->setCurrentBlock("deputies");

            foreach (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputies(self::dic()->user()->getId()) as $deputy) {
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
        $deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getDeputiesOf(self::dic()->user()->getId());
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
     *
     */
    public static function addTabs()/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId())) {
            self::dic()->tabs()->addTab(self::TAB_EDIT_DEPUTIES, self::plugin()->translate("my_deputies", self::LANG_MODULE), self::dic()->ctrl()
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

        self::dic()->tabs()->addTab(self::TAB_EDIT_DEPUTIES, self::plugin()->translate("my_deputies", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_EDIT_DEPUTIES));
    }


    /**
     *
     */
    protected function back()/*:void*/
    {
        self::dic()->ctrl()->redirectByClass(ilPersonalDesktopGUI::class, "jumpToProfile");
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

        $this->deputies = self::srUserEnrolment()->enrolmentWorkflow()->deputies()->storeUserDeputiesArray(self::dic()->user()->getId(), $form->getDeputies());

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
}
