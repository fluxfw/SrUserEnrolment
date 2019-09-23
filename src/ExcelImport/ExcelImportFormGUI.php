<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilCheckboxInputGUI;
use ilFileInputGUI;
use ilNonEditableValueGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineInputGUI\MultiLineInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TextInputGUI\TextInputGUIWithModernAutoComplete;
use srag\Plugins\SrUserEnrolment\Config\Config;
use srag\Plugins\SrUserEnrolment\Rule\Repository;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ExcelImportFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImportFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = ExcelImportGUI::LANG_MODULE_EXCEL_IMPORT;
    const KEY_COUNT_SKIP_TOP_ROWS = self::LANG_MODULE . "_count_skip_top_rows";
    const KEY_CREATE_NEW_USERS = self::LANG_MODULE . "_create_new_users";
    const KEY_FIELDS = self::LANG_MODULE . "_fields";
    const KEY_GENDER_F = self::LANG_MODULE . "_gender_f";
    const KEY_GENDER_M = self::LANG_MODULE . "_gender_m";
    const KEY_GENDER_N = self::LANG_MODULE . "_gender_n";
    const KEY_LOCAL_USER_ADMINISTRATION = self::LANG_MODULE . "_local_user_administration";
    const KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE = self::LANG_MODULE . "_local_user_administration_object_type";
    const KEY_LOCAL_USER_ADMINISTRATION_TYPE = self::LANG_MODULE . "_local_user_administration_type";
    const KEY_MAP_EXISTS_USERS_FIELD = self::LANG_MODULE . "_map_exists_users_field";
    const KEY_ORG_UNIT_ASSIGN = self::LANG_MODULE . "_org_unit_assign";
    const KEY_ORG_UNIT_ASSIGN_POSITION = self::LANG_MODULE . "_org_unit_assign_position";
    const KEY_ORG_UNIT_ASSIGN_TYPE = self::LANG_MODULE . "_org_unit_assign_type";
    const KEY_SET_PASSWORD = self::LANG_MODULE . "_set_password";
    /**
     * @var string
     */
    protected $excel_file = "";
    /**
     * @var int
     */
    protected $excel_import_count_skip_top_rows = 0;
    /**
     * @var bool
     */
    protected $excel_import_create_new_users = false;
    /**
     * @var array
     */
    protected $excel_import_fields = [];
    /**
     * @var string
     */
    protected $excel_import_gender_f = "";
    /**
     * @var string
     */
    protected $excel_import_gender_m = "";
    /**
     * @var string
     */
    protected $excel_import_gender_n = "";
    /**
     * @var bool
     */
    protected $excel_import_local_user_administration = false;
    /**
     * @var int
     */
    protected $excel_import_local_user_administration_object_type = ExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY;
    /**
     * @var int
     */
    protected $excel_import_local_user_administration_type = ExcelImport::LOCAL_USER_ADMINISTRATION_TYPE_TITLE;
    /**
     * @var int
     */
    protected $excel_import_map_exists_users_field = ExcelImport::MAP_EXISTS_USERS_LOGIN;
    /**
     * @var bool
     */
    protected $excel_import_org_unit_assign = false;
    /**
     * @var int
     */
    protected $excel_import_org_unit_assign_position = ExcelImport::ORG_UNIT_POSITION_FIELD;
    /**
     * @var int
     */
    protected $excel_import_org_unit_assign_type = ExcelImport::ORG_UNIT_TYPE_TITLE;
    /**
     * @var int
     */
    protected $excel_import_set_password = ExcelImport::SET_PASSWORD_RANDOM;


    /**
     * @return array
     */
    public static function getExcelImportFields() : array
    {
        return [
            self::KEY_COUNT_SKIP_TOP_ROWS => [
                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                "setTitle"              => self::plugin()->translate(self::KEY_COUNT_SKIP_TOP_ROWS),
                "setSuffix"             => self::plugin()->translate("rows", static::LANG_MODULE)
            ],

            self::KEY_FIELDS => [
                self::PROPERTY_CLASS    => MultiLineInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_MULTI    => true,
                "setShowLabel"          => true,
                self::PROPERTY_SUBITEMS => [
                    "type"           => [
                        self::PROPERTY_CLASS    => TypeSelectInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_OPTIONS  => [
                            ExcelImport::FIELDS_TYPE_ILIAS  => self::plugin()->translate(self::KEY_FIELDS . "_ilias"),
                            ExcelImport::FIELDS_TYPE_CUSTOM => self::plugin()->translate(self::KEY_FIELDS . "_custom")
                        ],
                        "setTitle"              => self::plugin()->translate(self::KEY_FIELDS . "_type")
                    ],
                    "key"            => [
                        self::PROPERTY_CLASS    => TextInputGUIWithModernAutoComplete::class,
                        self::PROPERTY_REQUIRED => true,
                        "setTitle"              => self::plugin()->translate(self::KEY_FIELDS . "_key"),
                        "setDataSource"         => self::dic()->ctrl()
                            ->getLinkTargetByClass(ExcelImportGUI::class, ExcelImportGUI::CMD_KEY_AUTOCOMPLETE, "", true)
                    ],
                    "column_heading" => [
                        self::PROPERTY_CLASS    => ilTextInputGUI::class,
                        self::PROPERTY_REQUIRED => false,
                        "setTitle"              => self::plugin()->translate(self::KEY_FIELDS . "_column_heading")
                    ],
                    "update"         => [
                        self::PROPERTY_CLASS    => ilSelectInputGUI::class, // TODO: ilCheckboxInputGUI not correctly works in MultiLineInputGUI
                        self::PROPERTY_REQUIRED => false,
                        self::PROPERTY_OPTIONS  => [
                            false => self::plugin()->translate("no", self::LANG_MODULE),
                            true  => self::plugin()->translate("yes", self::LANG_MODULE),
                        ],
                        "setTitle"              => self::plugin()->translate(self::KEY_FIELDS . "_update")
                    ]
                ],
                "setTitle"              => self::plugin()->translate(self::KEY_FIELDS)
            ],

            self::KEY_MAP_EXISTS_USERS_FIELD => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => [
                    ExcelImport::MAP_EXISTS_USERS_LOGIN => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD . "_login"),
                        "setInfo"            => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                            self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD . "_login"),
                            ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "login")
                        ])
                    ],
                    ExcelImport::MAP_EXISTS_USERS_EMAIL => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD . "_email"),
                        "setInfo"            => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                            self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD . "_email"),
                            ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "email")
                        ])
                    ]
                ],
                "setTitle"              => self::plugin()->translate(self::KEY_MAP_EXISTS_USERS_FIELD)
            ],

            self::KEY_CREATE_NEW_USERS => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate(self::KEY_CREATE_NEW_USERS),
                "setInfo"            => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                    self::plugin()->translate(self::KEY_CREATE_NEW_USERS),
                    implode(", ", array_map(function (string $field) : string {
                        return ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, $field);
                    }, ["login", "email", "firstname", "lastname"]))
                ])
            ],

            self::KEY_SET_PASSWORD => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => [
                    ExcelImport::SET_PASSWORD_RANDOM => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::plugin()->translate(self::KEY_SET_PASSWORD . "_random")
                    ],
                    ExcelImport::SET_PASSWORD_FIELD  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::plugin()->translate(self::KEY_SET_PASSWORD . "_field"),
                        "setInfo"            => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                            self::plugin()->translate(self::KEY_SET_PASSWORD . "_field"),
                            ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "passwd")
                        ])
                    ]
                ],
                "setTitle"              => self::plugin()->translate(self::KEY_SET_PASSWORD)
            ],

            self::KEY_GENDER_M => [
                self::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"           => self::plugin()->translate(self::KEY_GENDER_M)
            ],
            self::KEY_GENDER_F => [
                self::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"           => self::plugin()->translate(self::KEY_GENDER_F)
            ],
            self::KEY_GENDER_N => [
                self::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"           => self::plugin()->translate(self::KEY_GENDER_N)
            ],

            self::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint" => [
                self::PROPERTY_CLASS   => ilNonEditableValueGUI::class,
                self::PROPERTY_VALUE   => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint"),
                self::PROPERTY_NOT_ADD => self::ilias()->users()->isLocalUserAdminisrationEnabled(),
                "setTitle"             => ""
            ],
            self::KEY_LOCAL_USER_ADMINISTRATION                    => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE => [
                        self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_SUBITEMS => [
                            ExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE . "_category")
                            ],
                            ExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE . "_org_unit")
                            ]
                        ],
                        "setTitle"              => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE)
                    ],
                    self::KEY_LOCAL_USER_ADMINISTRATION_TYPE        => [
                        self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_SUBITEMS => [
                            ExcelImport::LOCAL_USER_ADMINISTRATION_TYPE_TITLE  => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_TYPE . "_title")
                            ],
                            ExcelImport::LOCAL_USER_ADMINISTRATION_TYPE_REF_ID => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_TYPE . "_ref_id")
                            ]
                        ],
                        "setTitle"              => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION_TYPE)
                    ]
                ],
                self::PROPERTY_NOT_ADD  => (!self::ilias()->users()->isLocalUserAdminisrationEnabled()),
                "setTitle"              => self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION),
                "setInfo"               => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                    self::plugin()->translate(self::KEY_LOCAL_USER_ADMINISTRATION),
                    ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "time_limit_owner")
                ])
            ],

            self::KEY_ORG_UNIT_ASSIGN => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_ORG_UNIT_ASSIGN_TYPE     => [
                        self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_SUBITEMS => [
                            ExcelImport::ORG_UNIT_TYPE_TITLE  => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_TYPE . "_title")
                            ],
                            ExcelImport::ORG_UNIT_TYPE_REF_ID => [
                                self::PROPERTY_CLASS => ilRadioOption::class,
                                "setTitle"           => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_TYPE . "_ref_id")
                            ]
                        ],
                        "setTitle"              => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_TYPE)
                    ],
                    self::KEY_ORG_UNIT_ASSIGN_POSITION => [
                        self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                        self::PROPERTY_REQUIRED => true,
                        self::PROPERTY_SUBITEMS => array_map(function (string $position) : array {
                                return [
                                    self::PROPERTY_CLASS => ilRadioOption::class,
                                    "setTitle"           => $position
                                ];
                            }, self::ilias()->orgUnits()->getPositions()) + [
                                ExcelImport::ORG_UNIT_POSITION_FIELD => [
                                    self::PROPERTY_CLASS => ilRadioOption::class,
                                    "setTitle"           => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_POSITION . "_field"),
                                    "setInfo"            => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                                        self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_POSITION . "_field"),
                                        ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "org_unit_position")
                                    ])
                                ]
                            ],
                        "setTitle"              => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN_POSITION)
                    ]
                ],
                "setTitle"              => self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN),
                "setInfo"               => self::plugin()->translate("fields_needed_field_info", self::LANG_MODULE, [
                    self::plugin()->translate(self::KEY_ORG_UNIT_ASSIGN),
                    ExcelImport::fieldName(ExcelImport::FIELDS_TYPE_ILIAS, "org_unit")
                ])
            ]
        ];
    }


    /**
     * @param PropertyFormGUI $form
     *
     * @return bool
     */
    public static function validateExcelImport(PropertyFormGUI $form) : bool
    {
        $error = false;

        $all_fields = ExcelImport::getAllFields();

        foreach ((array) $form->getInput(self::KEY_FIELDS) as $field) {
            if (!in_array($field["key"], $all_fields[$field["type"]])) {
                $error = true;

                $alerts = $form->getItemByPostVar(self::KEY_FIELDS)->getAlert();
                $alerts .= "<br>" . self::plugin()->translate("fields_invalid", self::LANG_MODULE, [
                        ExcelImport::fieldName($field["type"], $field["key"])
                    ]);

                $form->getItemByPostVar(self::KEY_FIELDS)->setAlert($alerts);
            }
        }

        $needed_fields = [];

        switch ($form->getInput(self::KEY_MAP_EXISTS_USERS_FIELD)) {
            case ExcelImport::MAP_EXISTS_USERS_LOGIN:
                $needed_fields[] = [
                    "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                    "key"         => "login",
                    "for_message" => self::KEY_MAP_EXISTS_USERS_FIELD
                ];
                break;

            case ExcelImport::MAP_EXISTS_USERS_EMAIL:
                $needed_fields[] = [
                    "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                    "key"         => "email",
                    "for_message" => self::KEY_MAP_EXISTS_USERS_FIELD
                ];
                break;

            default:
                break;
        }

        if ($form->getInput(self::KEY_CREATE_NEW_USERS)) {
            foreach (["login", "email", "firstname", "lastname"] as $key) {
                $needed_fields[] = [
                    "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                    "key"         => $key,
                    "for_message" => self::KEY_CREATE_NEW_USERS
                ];
            }
        }

        if (intval($form->getInput(self::KEY_SET_PASSWORD)) === ExcelImport::SET_PASSWORD_FIELD) {
            $needed_fields[] = [
                "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                "key"         => "passwd",
                "for_message" => self::KEY_SET_PASSWORD
            ];
        }

        if ($form->getInput(self::KEY_LOCAL_USER_ADMINISTRATION)) {
            $needed_fields[] = [
                "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                "key"         => "time_limit_owner",
                "for_message" => self::KEY_LOCAL_USER_ADMINISTRATION
            ];
        }

        if ($form->getInput(self::KEY_ORG_UNIT_ASSIGN)) {
            $needed_fields[] = [
                "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                "key"         => "org_unit",
                "for_message" => self::KEY_ORG_UNIT_ASSIGN
            ];

            if (intval($form->getInput(self::KEY_ORG_UNIT_ASSIGN_POSITION)) === ExcelImport::ORG_UNIT_POSITION_FIELD) {
                $needed_fields[] = [
                    "type"        => ExcelImport::FIELDS_TYPE_ILIAS,
                    "key"         => "org_unit_position",
                    "for_message" => self::KEY_ORG_UNIT_ASSIGN_POSITION
                ];
            }
        }

        foreach ($needed_fields as $needed_field) {
            $has_field = false;

            foreach ((array) $form->getInput(self::KEY_FIELDS) as $field) {
                if (intval($field["type"]) === $needed_field["type"] && trim($field["key"]) === $needed_field["key"]
                    && !empty(trim($field["column_heading"]))
                ) {
                    $has_field = true;
                    break;
                }
            }

            if (!$has_field) {
                $error = true;

                $alerts = $form->getItemByPostVar(self::KEY_FIELDS)->getAlert();
                $alerts .= "<br>" . self::plugin()->translate("fields_missing", self::LANG_MODULE, [
                        ExcelImport::fieldName($needed_field["type"], $needed_field["key"]),
                        self::plugin()->translate($needed_field["for_message"])
                    ]);

                $form->getItemByPostVar(self::KEY_FIELDS)->setAlert($alerts);
            }
        }

        return (!$error);
    }


    /**
     * @inheritdoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "excel_file":
                return $this->{$key};

            default:
                return Config::getField($key);
        }
    }


    /**
     * @inheritdoc
     */
    protected function initAction()/*: void*/
    {
        self::dic()->ctrl()->saveParameter($this->parent, Repository::GET_PARAM_REF_ID);

        parent::initAction();
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ExcelImportGUI::CMD_EXCEL_IMPORT, $this->txt("import"));
        $this->addCommandButton(ExcelImportGUI::CMD_BACK_TO_MEMBERS_LIST, $this->txt("cancel"));
    }


    /**
     * @inheritdoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
                "excel_file" => [
                    self::PROPERTY_CLASS    => ilFileInputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    "setSuffixes"           => [["xlsx", "xltx"]]
                ]
            ] + self::getExcelImportFields();
    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("title"));
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        return ($this->storeFormCheck() && self::validateExcelImport($this) && parent::storeForm());
    }


    /**
     * @inheritdoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "excel_file":
                $this->excel_file = strval($this->getInput("excel_file")["tmp_name"]);
                break;

            case self::KEY_LOCAL_USER_ADMINISTRATION . "_disabled_hint":
                return;

            default:
                $this->{$key} = $value;
                break;
        }
    }


    /**
     * @return string
     */
    public function getExcelFile() : string
    {
        return $this->excel_file;
    }


    /**
     * @return int
     */
    public function getCountSkipTopRows() : int
    {
        return $this->{self::KEY_COUNT_SKIP_TOP_ROWS};
    }


    /**
     * @return string
     */
    public function getGenderF() : string
    {
        return $this->{self::KEY_GENDER_F};
    }


    /**
     * @return string
     */
    public function getGenderM() : string
    {
        return $this->{self::KEY_GENDER_M};
    }


    /**
     * @return string
     */
    public function getGenderN() : string
    {
        return $this->{self::KEY_GENDER_N};
    }


    /**
     * @return int
     */
    public function getLocalUserAdministrationObjectType() : int
    {
        return $this->{self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE};
    }


    /**
     * @return int
     */
    public function getLocalUserAdministrationType() : int
    {
        return $this->{self::KEY_LOCAL_USER_ADMINISTRATION_TYPE};
    }


    /**
     * @return int
     */
    public function getMapExistsUsersField() : int
    {
        return $this->{self::KEY_MAP_EXISTS_USERS_FIELD};
    }


    /**
     * @return int
     */
    public function getOrgUnitPosition() : int
    {
        return $this->{self::KEY_ORG_UNIT_ASSIGN_POSITION};
    }


    /**
     * @return int
     */
    public function getOrgUnitType() : int
    {
        return $this->{self::KEY_ORG_UNIT_ASSIGN_TYPE};
    }


    /**
     * @return int
     */
    public function getSetPassword() : int
    {
        return $this->{self::KEY_SET_PASSWORD};
    }


    /**
     * @return bool
     */
    public function isCreateNewUsers() : bool
    {
        return $this->{self::KEY_CREATE_NEW_USERS};
    }


    /**
     * @return bool
     */
    public function isLocalUserAdministration() : bool
    {
        return $this->{self::KEY_LOCAL_USER_ADMINISTRATION};
    }


    /**
     * @return bool
     */
    public function isOrgUnitAssign() : bool
    {
        return $this->{self::KEY_ORG_UNIT_ASSIGN};
    }


    /**
     * @return array
     */
    public function getUserFields() : array
    {
        return $this->{self::KEY_FIELDS};
    }
}
