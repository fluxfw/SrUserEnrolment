<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RulesCourseSettingsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\RulesCourseSettingsGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\RulesCourseSettingsGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\User\RulesUserGUI
 */
class RulesCourseSettingsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_EDIT_SETTINGS = "editSettings";
    const CMD_UPDATE_SETTINGS = "updateSettings";
    const LANG_MODULE = RulesGUI::LANG_MODULE . "_settings";
    const TAB_SETTINGS = "settings";
    /**
     * @var RulesCourseGUI
     */
    protected $parent;
    /**
     * @var Settings
     */
    protected $settings;


    /**
     * RulesCourseSettingsGUI constructor
     *
     * @param RulesCourseGUI $parent
     */
    public function __construct(RulesCourseGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->settings = self::srUserEnrolment()->ruleEnrolment()->rules()->settings()->getSettings($this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()));

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_EDIT_SETTINGS:
                    case self::CMD_UPDATE_SETTINGS:
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
    public static function addTabs()/*:void*/
    {
        self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_EDIT_SETTINGS));
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     *
     */
    protected function editSettings()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srUserEnrolment()->ruleEnrolment()->rules()->settings()->factory()->newFormBuilderInstance($this, $this->settings);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateSettings()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srUserEnrolment()->ruleEnrolment()->rules()->settings()->factory()->newFormBuilderInstance($this, $this->settings);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_SETTINGS);
    }
}
