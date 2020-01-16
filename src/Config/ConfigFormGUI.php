<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilCheckboxInputGUI;
use ilMultiSelectInputGUI;
use ilSrUserEnrolmentConfigGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\RulesGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ConfigFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const KEY_ROLES = "roles";
    const KEY_SHOW_ASSISTANTS = "show_assistants";
    const KEY_SHOW_ENROLMENT_WORKFLOW = "show_enrolment_workflow";
    const KEY_SHOW_EXCEL_IMPORT = "show_excel_import";
    const KEY_SHOW_EXCEL_IMPORT_CONFIG = "show_excel_import_config";
    const KEY_SHOW_RESET_PASSWORD = "show_reset_password";
    const KEY_SHOW_RULES_ENROLL = "show_rules_enroll";
    const LANG_MODULE = ilSrUserEnrolmentConfigGUI::LANG_MODULE;


    /**
     * ConfigFormGUI constructor
     *
     * @param ilSrUserEnrolmentConfigGUI $parent
     */
    public function __construct(ilSrUserEnrolmentConfigGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return self::srUserEnrolment()->config()->getField($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilSrUserEnrolmentConfigGUI::CMD_UPDATE_CONFIGURE, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            self::KEY_ROLES                   => [
                self::PROPERTY_CLASS    => ilMultiSelectInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getAllRoles(),
                "enableSelectAll"       => true
            ],
            self::KEY_SHOW_RULES_ENROLL       => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("show", self::LANG_MODULE, [
                    self::plugin()->translate("title", RulesGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_EXCEL_IMPORT       => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                        self::KEY_SHOW_EXCEL_IMPORT_CONFIG => [
                            self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                        ]
                    ] + ExcelImportFormGUI::getExcelImportFields(new ExcelImportGUI()),
                "setTitle"              => self::plugin()->translate("show", self::LANG_MODULE, [
                    self::plugin()->translate("title", ExcelImportGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_RESET_PASSWORD     => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("show", self::LANG_MODULE, [
                    self::plugin()->translate("title", ResetPasswordGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_ENROLMENT_WORKFLOW => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_SHOW_ASSISTANTS => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setTitle"           => self::plugin()->translate("show", self::LANG_MODULE, [
                            self::plugin()->translate("assistants", AssistantsGUI::LANG_MODULE)
                        ])
                    ],
                ],
                "setTitle"              => self::plugin()->translate("show", self::LANG_MODULE, [
                    self::plugin()->translate("title", WorkflowsGUI::LANG_MODULE)
                ])
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("configuration"));
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        return ($this->storeFormCheck() && ($this->getInput(self::KEY_SHOW_EXCEL_IMPORT) ? ExcelImportFormGUI::validateExcelImport($this) : true)
            && parent::storeForm());
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case self::KEY_ROLES:
                if ($value[0] === "") {
                    array_shift($value);
                }

                $value = array_map(function (string $role_id) : int {
                    return intval($role_id);
                }, $value);

                self::srUserEnrolment()->config()->setField($key, $value);
                break;

            case ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint":
                break;

            default:
                self::srUserEnrolment()->config()->setField($key, $value);
                break;
        }
    }
}
