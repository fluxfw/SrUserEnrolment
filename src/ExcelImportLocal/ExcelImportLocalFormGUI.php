<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImportLocal;

use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;

/**
 * Class ExcelImportLocalFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImportLocal
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImportLocalFormGUI extends ExcelImportFormGUI
{

    /**
     * @inheritDoc
     */
    public function __construct($parent)
    {
        $this->excel_import_local_user_administration = true;

        switch ($a = self::dic()->objDataCache()->lookupType(self::rules()->getObjId())) {
            case "cat":
                $this->excel_import_local_user_administration_object_type = ExcelImportLocal::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY;
                break;

            case "orgu":
                $this->excel_import_local_user_administration_object_type = ExcelImportLocal::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT;
                break;

            default:
                break;
        }

        $this->excel_import_local_user_administration_type = ExcelImportLocal::LOCAL_USER_ADMINISTRATION_TYPE_REF_ID;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "excel_import_local_user_administration":
            case "excel_import_local_user_administration_object_type":
            case "excel_import_local_user_administration_type":
                return $this->{$key};

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
                case "excel_import_local_user_administration":
                    $field[self::PROPERTY_DISABLED] = true;
                    unset($field["setInfo"]);

                    foreach ($field[self::PROPERTY_SUBITEMS] as $subkey => &$subfield) {
                        $subfield[self::PROPERTY_DISABLED] = true;

                        switch ($subkey) {
                            case "excel_import_local_user_administration_type":
                                $subfield["setInfo"] = self::dic()->objDataCache()->lookupTitle(self::rules()->getObjId());
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
}
