<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport\User;

use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use stdClass;

/**
 * Class UserExcelImport
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport\User
 */
class UserExcelImport extends ExcelImport
{

    /**
     * @inheritDoc
     */
    public function getUsersToEnroll() : array
    {
        self::dic()->ctrl()->redirectByClass(UserExcelImportGUI::class, UserExcelImportGUI::CMD_BACK);

        return [];
    }


    /**
     * @inheritDoc
     */
    protected function getUpdateFields(array $fields) : array
    {
        $update_fields = parent::getUpdateFields($fields);

        switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
            case "cat":
            case "orgu":
                $update_fields[self::FIELDS_TYPE_ILIAS]["time_limit_owner"] = true;
                break;

            case "role":
            case "usrf":
            default:
                break;
        }

        return $update_fields;
    }


    /**
     * @inheritDoc
     */
    protected function handleLocalUserAdministration(ExcelImportFormGUI $form, stdClass &$user)/*: void*/
    {
        switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
            case "cat":
            case "orgu":
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = $this->parent->getObjRefId();
                break;

            case "role":
            case "usrf":
            default:
                parent::handleLocalUserAdministration($form, $user);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function handleRoles(ExcelImportFormGUI $form, stdClass &$user)/*: void*/
    {
        parent::handleRoles($form, $user);

        switch ($this->parent::getObjType($this->parent->getObjRefId(), $this->parent->getObjSingleId())) {
            case "role":
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->roles[] = $this->parent->getObjSingleId();
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->roles = array_unique($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->roles);
                break;

            case "cat":
            case "orgu":
            case "usrf":
            default:
                break;
        }
    }
}
