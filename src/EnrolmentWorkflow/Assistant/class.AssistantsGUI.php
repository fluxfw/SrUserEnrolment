<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilPersonalDesktopGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AssistantsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI: ilUIPluginRouterGUI
 */
class AssistantsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_EDIT_ASSISTANTS = "editAssistants";
    const CMD_UPDATE_ASSISTANTS = "updateAssistants";
    const LANG_MODULE = "assistants";
    const TAB_EDIT_ASSISTANTS = "edit_assistants";
    /**
     * @var Assistants
     */
    protected $assistants;


    /**
     * AssistantsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->assistants = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->getAssistantsForUser(self::dic()->user()->getId());

        if (!self::srUserEnrolment()->enrolmentWorkflow()->assistants()->hasAccess(self::dic()->user()->getId())) {
            die();
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_EDIT_ASSISTANTS:
                    case self::CMD_UPDATE_ASSISTANTS:
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
    public static function addTabs()/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->assistants()->hasAccess(self::dic()->user()->getId())) {
            self::dic()->tabs()->addTab(self::TAB_EDIT_ASSISTANTS, self::plugin()->translate("assistants", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_ASSISTANTS));
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

        self::dic()->tabs()->addTab(self::TAB_EDIT_ASSISTANTS, self::plugin()->translate("assistants", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_EDIT_ASSISTANTS));
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
    protected function editAssistants()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_ASSISTANTS);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newFormInstance($this, $this->assistants);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateAssistants()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_ASSISTANTS);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newFormInstance($this, $this->assistants);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_ASSISTANTS);
    }
}
