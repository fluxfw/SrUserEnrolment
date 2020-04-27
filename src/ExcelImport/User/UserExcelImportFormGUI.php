<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\User;

use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;

/**
 * Class UserExcelImportFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserExcelImportFormGUI extends ExcelImportFormGUI
{

    /**
     * @inheritDoc
     */
    public function __construct(UserExcelImportGUI $parent)
    {
        switch ($parent::getObjType($parent->getObjRefId(), $parent->getObjSingleId())) {
            case "role":
            case "usrf":
                $this->excel_import_local_user_administration = false;
                break;

            case "cat":
            case "orgu":
            default:
                $this->excel_import_local_user_administration = true;
                break;
        }

        switch ($parent::getObjType($parent->getObjRefId(), $parent->getObjSingleId())) {
            case "cat":
                $this->excel_import_local_user_administration_object_type = UserExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY;
                break;

            case "orgu":
                $this->excel_import_local_user_administration_object_type = UserExcelImport::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT;
                break;

            case "role":
            case "usrf":
            default:
                break;
        }

        $this->excel_import_local_user_administration_type = UserExcelImport::LOCAL_USER_ADMINISTRATION_TYPE_REF_ID;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case self::KEY_LOCAL_USER_ADMINISTRATION:
            case self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE:
            case self::KEY_LOCAL_USER_ADMINISTRATION_TYPE:
                return $this->{$key};

            case self::KEY_CREATE_NEW_USERS_GLOBAL_ROLES:
                $value = parent::getValue($key);

                switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
                    case "role":
                        $value[] = $this->parent->getObjSingleId();

                        $value = array_unique($value);
                        break;

                    case "cat":
                    case "orgu":
                    case "usrf":
                    default:
                        break;
                }

                return $value;

            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        parent::initFields();

        foreach ($this->fields as $key => &$field) {
            switch ($key) {
                case self::KEY_LOCAL_USER_ADMINISTRATION:
                    $field[self::PROPERTY_DISABLED] = true;

                    switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
                        case "role":
                        case "usrf":
                            $field["setInfo"] = $this->parent->getBackTitle();
                            break;

                        case "cat":
                        case "orgu":
                        default:
                            unset($field["setInfo"]);
                            break;
                    }

                    foreach ($field[self::PROPERTY_SUBITEMS] as $subkey => &$subfield) {
                        $subfield[self::PROPERTY_DISABLED] = true;

                        switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
                            case "role":
                            case "usrf":
                                break;

                            case "cat":
                            case "orgu":
                            default:
                                switch ($subkey) {
                                    case self::KEY_LOCAL_USER_ADMINISTRATION_TYPE:
                                        $subfield["setInfo"] = $this->parent->getBackTitle();
                                        break;

                                    default:
                                        break;
                                }
                                break;
                        }
                    }
                    break;

                case self::KEY_CREATE_NEW_USERS:
                    foreach ($field[self::PROPERTY_SUBITEMS] as $subkey => &$subfield) {
                        switch ($subkey) {
                            case self::KEY_CREATE_NEW_USERS_GLOBAL_ROLES:
                                switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
                                    case "role":
                                        $subfield["setInfo"] = $this->parent->getBackTitle();
                                        break;

                                    case "cat":
                                    case "orgu":
                                    case "usrf":
                                    default:
                                        break;
                                }
                                break;

                            default:
                                break;
                        }
                    }
                    break;

                default:
                    break;
            }
        }
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case self::KEY_LOCAL_USER_ADMINISTRATION:
            case self::KEY_LOCAL_USER_ADMINISTRATION_OBJECT_TYPE:
            case self::KEY_LOCAL_USER_ADMINISTRATION_TYPE:
                break;

            case self::KEY_CREATE_NEW_USERS_GLOBAL_ROLES:
                switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
                    case "role":
                        $value[] = $this->parent->getObjSingleId();

                        $value = array_unique($value);
                        break;

                    case "cat":
                    case "orgu":
                    case "usrf":
                    default:
                        break;
                }

                parent::storeValue($key, $value);
                break;

            default:
                parent::storeValue($key, $value);
                break;
        }
    }
}
