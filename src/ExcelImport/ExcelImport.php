<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use Closure;
use ilCalendarSettings;
use ilDBConstants;
use ilExcel;
use ilObjectFactory;
use ilObjUser;
use ilSession;
use ilSrUserEnrolmentPlugin;
use ilUserDefinedFields;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Log\LogsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;
use Throwable;

/**
 * Class ExcelImport
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ExcelImport
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const FIELDS_TYPE_CUSTOM = 2;
    const FIELDS_TYPE_ILIAS = 1;
    const LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY = 1;
    const LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT = 2;
    const LOCAL_USER_ADMINISTRATION_TYPE_REF_ID = 2;
    const LOCAL_USER_ADMINISTRATION_TYPE_TITLE = 1;
    const MAP_EXISTS_USERS_EMAIL = 2;
    const MAP_EXISTS_USERS_LOGIN = 1;
    const ORG_UNIT_POSITION_FIELD = 0;
    const ORG_UNIT_TYPE_REF_ID = 2;
    const ORG_UNIT_TYPE_TITLE = 1;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const SESSION_KEY = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_excel_import";
    const SET_PASSWORD_FIELD = 2;
    const SET_PASSWORD_RANDOM = 1;
    /**
     * @var ExcelImportGUI
     */
    protected $parent;


    /**
     * ExcelImport constructor
     *
     * @param ExcelImportGUI $parent
     */
    public function __construct(ExcelImportGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     * https://stackoverflow.com/questions/1993721/how-to-convert-pascalcase-to-pascal-case
     *
     * @param string $string
     *
     * @return string
     */
    public static function camelCaseToStr($string)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }


    /**
     * @param int    $type
     * @param string $key
     *
     * @return string
     */
    public static function fieldName(int $type, string $key) : string
    {
        switch ($type) {
            case ExcelImport::FIELDS_TYPE_ILIAS:
                $type = self::plugin()->translate(ExcelImportFormGUI::KEY_FIELDS . "_ilias");
                break;

            case ExcelImport::FIELDS_TYPE_CUSTOM:
                $type = self::plugin()->translate(ExcelImportFormGUI::KEY_FIELDS . "_custom");
                break;

            default:
                $type = "";
        }

        return $type . " / " . $key;
    }


    /**
     * @return array
     */
    public static function getAllFields() : array
    {
        return [
            self::FIELDS_TYPE_ILIAS  => self::getFieldsForType(self::FIELDS_TYPE_ILIAS),
            self::FIELDS_TYPE_CUSTOM => self::getFieldsForType(self::FIELDS_TYPE_CUSTOM)
        ];
    }


    /**
     * @param int    $type
     * @param string $term
     *
     * @return string[]
     */
    public static function getFieldsForType(int $type, string $term = "") : array
    {
        switch ($type) {
            case self::FIELDS_TYPE_ILIAS:
                $fields = array_merge(array_map(function (string $method) : string {
                    return self::camelCaseToStr(substr($method, 3));
                }, array_filter(get_class_methods(ilObjUser::class), function (string $method) {
                    return (strpos($method, "set") === 0);
                })), [
                    "org_unit",
                    "org_unit_position"
                ]);
                break;

            case self::FIELDS_TYPE_CUSTOM:
                $fields = array_map(function (array $field) : string {
                    return $field["field_name"];
                }, ilUserDefinedFields::_getInstance()->getDefinitions());
                break;

            default:
                $fields = [];
                break;
        }

        $fields = array_filter($fields, function (string $property) use ($term): bool {
            return ((empty($term) || stripos($property, $term) !== false));
        });

        natcasesort($fields);
        $fields = array_values($fields);

        return $fields;
    }


    /**
     *
     */
    public function clean()/*: void*/
    {
        ilSession::clear(ExcelImport::SESSION_KEY);
    }


    /**
     * @return string
     */
    public function createOrUpdateUsers() : string
    {
        $data = (object) json_decode(ilSession::get(self::SESSION_KEY));
        $users = (array) $data->users;

        foreach ($users as &$user) {
            try {
                if ($user->is_new) {
                    $user->ilias_user_id = self::srUserEnrolment()->excelImport()->createNewAccount($user->{ExcelImportFormGUI::KEY_FIELDS});

                    self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()
                        ->factory()
                        ->newObjectRuleUserInstance($this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()), $user->ilias_user_id)
                        ->withStatus(Log::STATUS_USER_CREATED)
                        ->withMessage("User data: " . json_encode($user)));
                } else {
                    if (self::srUserEnrolment()->excelImport()->updateUserAccount($user->ilias_user_id, $user->{ExcelImportFormGUI::KEY_FIELDS})) {
                        self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()
                            ->factory()
                            ->newObjectRuleUserInstance($this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()), $user->ilias_user_id)
                            ->withStatus(Log::STATUS_USER_UPDATED)
                            ->withMessage("User data: " . json_encode($user)));
                    }
                }
            } catch (Throwable $ex) {
                self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()->logs()
                    ->factory()
                    ->newExceptionInstance($ex, $this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()))->withStatus(Log::STATUS_USER_FAILED));
            }
        }

        $data = (object) [
            "users" => $users
        ];

        ilSession::set(self::SESSION_KEY, json_encode($data));

        $logs = array_reduce(array_keys(Log::$status_create_or_update_users), function (array $logs, int $status) : array {
            $logs[] = self::plugin()->translate("status_" . Log::$status_create_or_update_users[$status], LogsGUI::LANG_MODULE) . ": " . count(self::srUserEnrolment()->logs()->getKeptLogs($status));

            return $logs;
        }, []);

        return nl2br(implode("\n", $logs), false);
    }


    /**
     * @return string
     */
    public function enroll() : string
    {
        $data = (object) json_decode(ilSession::get(self::SESSION_KEY));
        $users = (array) $data->users;

        foreach ($users as &$user) {
            try {
                if (self::srUserEnrolment()->ruleEnrolment()->enroll($this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()), $user->ilias_user_id)) {
                    self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()
                        ->logs()
                        ->factory()
                        ->newObjectRuleUserInstance($this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()), $user->ilias_user_id)
                        ->withStatus(Log::STATUS_ENROLLED)
                        ->withMessage("User data: " . json_encode($user)));
                }
            } catch (Throwable $ex) {
                self::srUserEnrolment()->logs()->storeLog(self::srUserEnrolment()
                    ->logs()
                    ->factory()
                    ->newExceptionInstance($ex, $this->parent::getObjId($this->parent->getObjRefId(), $this->parent->getObjSingleId()))->withStatus(Log::STATUS_ENROLL_FAILED));
            }
        }

        $logs = array_reduce(array_keys(Log::$status_enroll), function (array $logs, int $status) : array {
            $logs[] = self::plugin()->translate("status_" . Log::$status_enroll[$status], LogsGUI::LANG_MODULE) . ": " . count(self::srUserEnrolment()->logs()->getKeptLogs($status));

            return $logs;
        }, []);

        return nl2br(implode("\n", $logs), false);
    }


    /**
     * @return array
     */
    public function getUsersToEnroll() : array
    {
        $data = (object) json_decode(ilSession::get(self::SESSION_KEY));
        $users = (array) $data->users;

        $obj = ilObjectFactory::getInstanceByRefId($this->parent->getObjRefId(), false);

        $users = array_filter($users, function (stdClass $user) use ($obj): bool {
            return (!empty($user->ilias_user_id) && !self::srUserEnrolment()->ruleEnrolment()->isEnrolled($obj->getId(), $user->ilias_user_id));
        });

        $data = (object) [
            "users" => $users
        ];

        ilSession::set(self::SESSION_KEY, json_encode($data));

        $users = array_map(function (stdClass $user) : string {
            return ilObjUser::_lookupLogin($user->ilias_user_id);
        }, $users);

        return $users;
    }


    /**
     * @param ExcelImportFormGUI $form
     *
     * @return array
     */
    public function parse(ExcelImportFormGUI $form) : array
    {
        $excel = new ilExcel();

        $excel->loadFromFile($form->getExcelFile());

        $rows = $excel->getSheetAsArray();

        /**
         * @var Spreadsheet $spreadsheet
         */
        $spreadsheet = Closure::bind(function ()/* : Spreadsheet*/ {
            return $this->workbook;
        }, $excel, ilExcel::class)();

        $rows = array_slice($rows, $form->getCountSkipTopRows());

        $fields = array_filter(array_map(function (array $field) : stdClass {
            $field = (object) $field;

            $field->type = intval($field->type);
            $field->key = trim($field->key);
            $field->column_heading = trim($field->column_heading);
            $field->update = boolval($field->update);

            return $field;
        }, $form->getUserFields()), function (stdClass $field) : bool {
            return (!empty($field->type) && !empty($field->key) && !empty($field->column_heading));
        });

        $update_fields = $this->getUpdateFields($fields);

        $columns_map = array_reduce($fields, function (array $columns_map, stdClass $field) : array {
            if (!isset($columns_map[$field->column_heading])) {
                $columns_map[$field->column_heading] = [];
            }

            $columns_map[$field->column_heading][] = $field;

            return $columns_map;
        }, []);

        $head = array_shift($rows);
        $columns = array_map(function (/*string*/ $column) use ($columns_map): array {
            if (isset($columns_map[$column])) {
                return $columns_map[$column];
            } else {
                return [];
            }
        }, $head);

        $users = [];

        foreach ($rows as $rowId => $row) {
            $user = (object) [
                "ilias_user_id"                => null,
                "is_new"                       => false,
                ExcelImportFormGUI::KEY_FIELDS => [
                    self::FIELDS_TYPE_ILIAS  => [],
                    self::FIELDS_TYPE_CUSTOM => []
                ]
            ];

            $has_user_data = false;
            foreach ($row as $cellI => $cell) {
                foreach ($columns[$cellI] as $field) {
                    $value = trim(strval($cell));
                    if (!empty($value)) {
                        $has_user_data = true;
                        $user->{ExcelImportFormGUI::KEY_FIELDS}[$field->type][$field->key] = $value;
                        if ($field->type === self::FIELDS_TYPE_ILIAS && $field->key === "passwd") {
                            // RegExp from libs/composer/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php::toFormattedString::646
                            $matches = [];
                            $cell_ = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(($cellI + 1), ($form->getCountSkipTopRows() + 1 + $rowId + 1));
                            preg_match('/(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy](?=(?:[^"]|"[^"]*")*$)/miu',
                                $spreadsheet->getCellXfByIndex($cell_->getXfIndex())
                                    ->getNumberFormat()->getFormatCode(), $matches);
                            if (is_array($matches) && count($matches) > 0) {
                                $user->{ExcelImportFormGUI::KEY_FIELDS}[$field->type][$field->key . "__original_date_value"] = $cell_->getValue();
                            }
                        }
                    }
                }
            }

            if ($has_user_data) {
                $user->{ExcelImportFormGUI::KEY_FIELDS} = json_decode(json_encode($user->{ExcelImportFormGUI::KEY_FIELDS}));
                $users[] = $user;
            }
        }

        $users = array_map(function (stdClass $user) use ($form): stdClass {
            switch ($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender) {
                case $form->getGenderM():
                    $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "m";
                    break;

                case $form->getGenderF():
                    $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "f";
                    break;

                case $form->getGenderN():
                    $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "n";
                    break;

                default:
                    $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->gender = "";
                    break;
            }

            if ($form->getSetPassword() === self::SET_PASSWORD_FIELD) {
                if ($form->isSetPasswordFormatDateTime() && !empty($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd__original_date_value)) {
                    $this->handleSetPasswordFormatDateTime($user);
                }
            } else {
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd = null;
            }
            unset($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd__original_date_value);

            if (self::srUserEnrolment()->excelImport()->isLocalUserAdminisrationEnabled() && $form->isLocalUserAdministration()) {
                $this->handleLocalUserAdministration($form, $user);
            } else {
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = null;
            }

            if ($form->isOrgUnitAssign()) {
                $this->handleOrgUnitAssign($user, $form);
            } else {
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = null;
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = null;
            }

            return $user;
        }, $users);

        $exists_users = array_filter($users, function (stdClass &$user) use ($form, $update_fields): bool {
            switch ($form->getMapExistsUsersField()) {
                case self::MAP_EXISTS_USERS_LOGIN:
                    if (!empty($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->login)) {
                        $user->ilias_user_id = self::srUserEnrolment()->excelImport()->getUserIdByLogin(strval($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->login));
                    }
                    break;

                case self::MAP_EXISTS_USERS_EMAIL:
                    if (!empty($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->email)) {
                        $user->ilias_user_id = self::srUserEnrolment()->excelImport()->getUserIdByEmail(strval($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->email));
                    }
                    break;

                default:
                    break;
            }

            if (empty($user->ilias_user_id)) {
                $user->is_new = true;

                return false;
            } else {
                $fields = $user->{ExcelImportFormGUI::KEY_FIELDS};
                foreach ($fields as $type => &$fields_) {
                    foreach ($fields_ as $key => $value) {
                        if ($update_fields[$type][$key] === false) {
                            unset($fields_->{$key});
                        }
                    }
                }

                return true;
            }
        });

        if ($form->isCreateNewUsers()) {
            $new_users = array_filter($users, function (stdClass &$user) use ($form): bool {
                if ($user->is_new) {
                    $this->handleRoles($form, $user);

                    return true;
                } else {
                    unset($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->roles);

                    return false;
                }
            });
        } else {
            $new_users = [];
        }

        $users = array_merge($new_users, $exists_users);

        $data = (object) [
            "users" => $users
        ];

        ilSession::set(self::SESSION_KEY, json_encode($data));

        $users = array_map(function (stdClass $user) : array {
            $items = [];
            foreach ($user->{ExcelImportFormGUI::KEY_FIELDS} as $type => $fields) {
                foreach ($fields as $key => $value) {
                    $items[self::fieldName($type, $key)] = is_array($value) ? implode(", ", $value) : strval($value);
                }
            }

            if (!empty(array_filter($items))) {
                return [
                    self::plugin()
                        ->translate($user->is_new ? "create_user" : "update_user", ExcelImportGUI::LANG_MODULE),
                    ":",
                    self::dic()->ui()->factory()->listing()->descriptive($items)
                ];
            } else {
                return [];
            }
        }, $users);

        return $users;
    }


    /**
     * @param array $fields
     *
     * @return array
     */
    protected function getUpdateFields(array $fields) : array
    {
        return array_reduce($fields, function (array $fields, stdClass $field) : array {
            $fields[$field->type][$field->key] = $field->update;

            return $fields;
        }, [
            self::FIELDS_TYPE_ILIAS  => [],
            self::FIELDS_TYPE_CUSTOM => []
        ]);
    }


    /**
     * @param ExcelImportFormGUI $form
     * @param stdClass           $user
     */
    protected function handleLocalUserAdministration(ExcelImportFormGUI $form, stdClass &$user)/*: void*/
    {
        $value = $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner;

        $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = null;

        if (!empty($value)) {
            switch ($form->getLocalUserAdministrationObjectType()) {
                case self::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_CATEGORY:
                    $obj_type = "cat";
                    break;

                case self::LOCAL_USER_ADMINISTRATION_OBJECT_TYPE_ORG_UNIT:
                    $obj_type = "orgu";
                    break;

                default:
                    $obj_type = "";
                    break;
            }

            if (!empty($obj_type)) {
                $wheres = ['type=%s'];
                $types = [ilDBConstants::T_TEXT];
                $values = [$obj_type];

                switch ($form->getLocalUserAdministrationType()) {
                    case self::LOCAL_USER_ADMINISTRATION_TYPE_TITLE:
                        $wheres[] = self::dic()->database()->like("title", ilDBConstants::T_TEXT, '%' . $value . '%');
                        break;

                    case self::LOCAL_USER_ADMINISTRATION_TYPE_REF_ID:
                        $wheres[] = "ref_id=%s";
                        $types[] = ilDBConstants::T_INTEGER;
                        $values[] = $value;
                        break;

                    default:
                        break;
                }

                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->time_limit_owner = self::srUserEnrolment()->excelImport()->getObjectRefIdByFilter($wheres, $types, $values);
            }
        }
    }


    /**
     * @param stdClass           $user
     * @param ExcelImportFormGUI $form
     */
    protected function handleOrgUnitAssign(stdClass &$user, ExcelImportFormGUI $form)
    {
        $value = $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit;

        $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = null;

        if (!empty($value)) {
            $wheres = ['type=%s'];
            $types = [ilDBConstants::T_TEXT];
            $values = ["orgu"];

            switch ($form->getOrgUnitType()) {
                case self::ORG_UNIT_TYPE_TITLE:
                    $wheres[] = self::dic()->database()->like("title", ilDBConstants::T_TEXT, '%%' . $value . '%%');
                    break;

                case self::ORG_UNIT_TYPE_REF_ID:
                    $wheres[] = "ref_id=%s";
                    $types[] = ilDBConstants::T_INTEGER;
                    $values[] = $value;
                    break;

                default:
                    break;
            }

            $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit = self::srUserEnrolment()->excelImport()->getObjectRefIdByFilter($wheres, $types, $values);
        }

        if ($form->getOrgUnitPosition() === self::ORG_UNIT_POSITION_FIELD) {
            $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = self::srUserEnrolment()->excelImport()
                ->getPositionIdByTitle($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position);
        } else {
            $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->org_unit_position = $form->getOrgUnitPosition();
        }
    }


    /**
     * @param ExcelImportFormGUI $form
     * @param stdClass           $user
     */
    protected function handleRoles(ExcelImportFormGUI $form, stdClass &$user)/*:void*/
    {
        $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->roles = array_unique($form->getExcelImportCreateNewUsersGlobalRoles());
    }


    /**
     * @param stdClass $user
     */
    protected function handleSetPasswordFormatDateTime(stdClass &$user)/*: void*/
    {
        $date = Date::excelToDateTimeObject($user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd__original_date_value);

        // Modules/DataCollection/classes/Fields/Datetime/class.ilDclDatetimeRecordRepresentation.php::formatDate
        switch (self::dic()->user()->getDateFormat()) { // Assume date format for current user which has uploaded the excel file
            case ilCalendarSettings::DATE_FORMAT_DMY:
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd = $date->format("d.m.Y");
                break;

            case ilCalendarSettings::DATE_FORMAT_YMD:
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd = $date->format("Y-m-d");
                break;

            case ilCalendarSettings::DATE_FORMAT_MDY:
                $user->{ExcelImportFormGUI::KEY_FIELDS}->{self::FIELDS_TYPE_ILIAS}->passwd = $date->format("m/d/Y");
                break;

            default:
                break;
        }
    }
}
