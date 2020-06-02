<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\Form;

use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\RulesCourseSettingsGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\Settings;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\Settings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var Settings
     */
    protected $settings;


    /**
     * @inheritDoc
     *
     * @param RulesCourseSettingsGUI $parent
     * @param Settings               $settings
     */
    public function __construct(RulesCourseSettingsGUI $parent, Settings $settings)
    {
        $this->settings = $settings;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            RulesCourseSettingsGUI::CMD_UPDATE_SETTINGS => self::plugin()->translate("save", RulesCourseSettingsGUI::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [];

        foreach (array_keys($this->getFields()) as $key) {
            $data[$key] = Items::getter($this->settings, $key);
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "unenroll"           => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("unenroll", RulesCourseSettingsGUI::LANG_MODULE))->withByline(self::plugin()
                ->translate("unenroll_info", RulesCourseSettingsGUI::LANG_MODULE)),
            "update_enroll_type" => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate("update_enroll_type", RulesCourseSettingsGUI::LANG_MODULE))->withByline(self::plugin()
                ->translate("update_enroll_type_info", RulesCourseSettingsGUI::LANG_MODULE))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings", RulesCourseSettingsGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        foreach (array_keys($this->getFields()) as $key) {
            Items::setter($this->settings, $key, $data[$key]);
        }

        self::SrUserEnrolment()->ruleEnrolment()->rules()->settings()->storeSettings($this->settings);
    }
}
