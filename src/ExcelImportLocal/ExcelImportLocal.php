<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImportLocal;

use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportFormGUI;
use stdClass;

/**
 * Class ExcelImportLocal
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImportLocal
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImportLocal extends ExcelImport
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
        $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = self::rules()->getRefId();
    }


    /**
     * @inheritDoc
     */
    public function getUsersToEnroll() : string
    {
        self::dic()->ctrl()->redirectByClass(ExcelImportLocalGUI::class, ExcelImportLocalGUI::CMD_BACK_TO_MEMBERS_LIST);

        return "";
    }
}
