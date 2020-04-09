<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\User;

use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use stdClass;

/**
 * Class UserExcelImport
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserExcelImport extends ExcelImport
{

    /**
     * @inheritDoc
     */
    protected function getUpdateFields(array $fields) : array
    {
        $update_fields = parent::getUpdateFields($fields);

        $update_fields[self::FIELDS_TYPE_ILIAS]["time_limit_owner"] = true;

        return $update_fields;
    }


    /**
     * @inheritDoc
     */
    protected function handleLocalUserAdministration(ExcelImportFormGUI $form, stdClass &$user)/*: void*/
    {
        switch (self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($form->getParent()->getObjRefId()))) {
            case "usrf":
                break;

            case "cat":
            case "orgu":
            default:
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = $this->obj_ref_id;
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function getUsersToEnroll() : array
    {
        self::dic()->ctrl()->redirectByClass(UserExcelImportGUI::class, UserExcelImportGUI::CMD_BACK);

        return [];
    }
}
