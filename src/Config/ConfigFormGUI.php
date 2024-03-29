<?php

namespace srag\Plugins\SrUserEnrolment\Config;

use ilCheckboxInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\DeputiesGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI;
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
 */
class ConfigFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const KEY_ROLES = "roles";
    const KEY_ROLES_READ_REQUESTS = "roles_read_requests";
    const KEY_SHOW_ASSISTANTS = "show_assistants";
    const KEY_SHOW_ASSISTANTS_SUPERVISORS = "show_assistants_supervisors";
    const KEY_SHOW_DEPUTIES = "show_deputies";
    const KEY_SHOW_ENROLMENT_WORKFLOW = "show_enrolment_workflow";
    const KEY_SHOW_EXCEL_IMPORT = "show_excel_import";
    const KEY_SHOW_EXCEL_IMPORT_CONFIG = "show_excel_import_config";
    const KEY_SHOW_EXCEL_IMPORT_COURSE = "show_excel_import_course";
    const KEY_SHOW_EXCEL_IMPORT_USER = "show_excel_import_local";
    const KEY_SHOW_EXCEL_IMPORT_USER_VIEW = "show_excel_import_local_type";
    const KEY_SHOW_MEMBERS = "show_members";
    const KEY_SHOW_RESET_PASSWORD = "show_reset_password";
    const KEY_SHOW_RULES_ENROLL = "show_rules_enroll";
    const KEY_SHOW_RULES_ENROLL_COURSE = "show_rules_enroll_course";
    const KEY_SHOW_RULES_ENROLL_USER = "show_rules_enroll_user";
    const LANG_MODULE = ConfigCtrl::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const SHOW_EXCEL_IMPORT_USER_TYPE_REPLACE = 2;
    const SHOW_EXCEL_IMPORT_USER_TYPE_SEPARATE = 1;


    /**
     * ConfigFormGUI constructor
     *
     * @param ConfigCtrl $parent
     */
    public function __construct(ConfigCtrl $parent)
    {
        parent::__construct($parent);
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
    protected function getValue(string $key)
    {
        switch ($key) {
            default:
                return self::srUserEnrolment()->config()->getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands() : void
    {
        $this->addCommandButton(ConfigCtrl::CMD_UPDATE_CONFIGURE, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        $excel_import_fields = ExcelImportFormGUI::getExcelImportFields(new ExcelImportGUI());
        $excel_import_fields[ExcelImportFormGUI::KEY_CREATE_NEW_USERS][self::PROPERTY_SUBITEMS][ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES]["setInfo"] = self::plugin()
            ->translate(ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES . "_info");
        $excel_import_fields[ExcelImportFormGUI::KEY_CREATE_NEW_USERS][self::PROPERTY_SUBITEMS][ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES_EXCLUDE] = [
            self::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
            self::PROPERTY_OPTIONS => self::srUserEnrolment()->ruleEnrolment()->getAllRoles(),
            "setTitle"             => self::plugin()->translate(ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES_EXCLUDE),
            "setInfo"              => self::plugin()->translate(ExcelImportFormGUI::KEY_CREATE_NEW_USERS_GLOBAL_ROLES_EXCLUDE . "_info")
        ];

        $this->fields = [
            self::KEY_ROLES                   => [
                self::PROPERTY_CLASS    => MultiSelectSearchNewInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getAllRoles()
            ],
            self::KEY_SHOW_RULES_ENROLL       => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_SHOW_RULES_ENROLL_COURSE => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ],
                    self::KEY_SHOW_RULES_ENROLL_USER   => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setInfo"            => self::plugin()->translate(self::KEY_SHOW_RULES_ENROLL_USER . "_info", self::LANG_MODULE, [
                            self::dic()->language()->txt("obj_role")
                        ])
                    ]
                ],
                "setTitle"              => self::plugin()->translate("enable", self::LANG_MODULE, [
                    self::plugin()->translate("type_course_rule", RulesGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_EXCEL_IMPORT       => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                        self::KEY_SHOW_EXCEL_IMPORT_COURSE => [
                            self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                        ],
                        self::KEY_SHOW_EXCEL_IMPORT_USER   => [
                            self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                            self::PROPERTY_SUBITEMS => [
                                self::KEY_SHOW_EXCEL_IMPORT_USER_VIEW => [
                                    self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                                    self::PROPERTY_REQUIRED => true,
                                    self::PROPERTY_SUBITEMS => [
                                        self::SHOW_EXCEL_IMPORT_USER_TYPE_SEPARATE => [
                                            self::PROPERTY_CLASS => ilRadioOption::class,
                                            "setTitle"           => $this->txt(self::KEY_SHOW_EXCEL_IMPORT_USER_VIEW . "_separate")
                                        ],
                                        self::SHOW_EXCEL_IMPORT_USER_TYPE_REPLACE  => [
                                            self::PROPERTY_CLASS => ilRadioOption::class,
                                            "setTitle"           => self::plugin()->translate(self::KEY_SHOW_EXCEL_IMPORT_USER_VIEW . "_replace", self::LANG_MODULE, [
                                                self::dic()->language()->txt("import_users")
                                            ])
                                        ]
                                    ]
                                ]
                            ],
                            "setInfo"               => self::plugin()->translate(self::KEY_SHOW_EXCEL_IMPORT_USER . "_info", self::LANG_MODULE, [
                                self::dic()->language()->txt("obj_cat"),
                                self::dic()->language()->txt("obj_orgu"),
                                self::dic()->language()->txt("obj_role"),
                                self::dic()->language()->txt("obj_usrf")
                            ])
                        ],
                        self::KEY_SHOW_EXCEL_IMPORT_CONFIG => [
                            self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                        ]
                    ] + $excel_import_fields,
                "setTitle"              => self::plugin()->translate("enable", self::LANG_MODULE, [
                    self::plugin()->translate("title", ExcelImportGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_RESET_PASSWORD     => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("enable", self::LANG_MODULE, [
                    self::plugin()->translate("title", ResetPasswordGUI::LANG_MODULE)
                ])
            ],
            self::KEY_SHOW_ENROLMENT_WORKFLOW => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_ROLES_READ_REQUESTS         => [
                        self::PROPERTY_CLASS    => MultiSelectSearchNewInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_OPTIONS  => self::srUserEnrolment()->ruleEnrolment()->getAllRoles()
                    ],
                    self::KEY_SHOW_ASSISTANTS             => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setTitle"           => self::plugin()->translate("enable", self::LANG_MODULE, [
                            self::plugin()->translate("assistants", AssistantsGUI::LANG_MODULE)
                        ]),
                        "setInfo"            => self::plugin()->translate("info", AssistantsGUI::LANG_MODULE)
                    ],
                    self::KEY_SHOW_ASSISTANTS_SUPERVISORS => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setTitle"           => self::plugin()->translate("enable", self::LANG_MODULE, [
                            self::plugin()->translate("assistants_supervisors", AssistantsGUI::LANG_MODULE)
                        ]),
                        "setInfo"            => self::plugin()->translate("assistants_supervisors_info", AssistantsGUI::LANG_MODULE)
                    ],
                    self::KEY_SHOW_DEPUTIES               => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setTitle"           => self::plugin()->translate("enable", self::LANG_MODULE, [
                            self::plugin()->translate("deputies", DeputiesGUI::LANG_MODULE)
                        ]),
                        "setInfo"            => self::plugin()->translate("info", DeputiesGUI::LANG_MODULE)
                    ],
                    self::KEY_SHOW_MEMBERS                => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setTitle"           => self::plugin()->translate("enable", self::LANG_MODULE, [
                            self::plugin()->translate("members", MembersGUI::LANG_MODULE)
                        ]),
                        "setInfo"            => self::plugin()->translate("info", MembersGUI::LANG_MODULE)
                    ]
                ],
                "setTitle"              => self::plugin()->translate("enable", self::LANG_MODULE, [
                    self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE)
                ])
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {
        $this->setTitle($this->txt("configuration"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value) : void
    {
        switch ($key) {
            case ExcelImportFormGUI::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint":
                break;

            default:
                self::srUserEnrolment()->config()->setValue($key, $value);
                break;
        }
    }
}
