<?php

namespace srag\Plugins\SrUserEnrolment\Log;

use ilDateTime;
use ilObjUser;
use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\DateDurationInputGUI\DateDurationInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class LogsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\Log
 */
class LogsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = LogsGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * LogsTableGUI constructor
     *
     * @param LogsGUI $parent
     * @param string  $parent_cmd
     */
    public function __construct(LogsGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "date"            => "date",
            "status"          => "status",
            "message"         => "message",
            "user_id"         => "user_id",
            "execute_user_id" => "execute_user_id"
        ];

        $columns = array_map(function (string $key) : array {
            return [
                "id"      => $key,
                "default" => true,
                "sort"    => true
            ];
        }, $columns);

        return $columns;
    }


    /**
     * @inheritDoc
     *
     * @param Log $row
     */
    protected function getColumnValue(string $column, /*Log*/ $row, int $format = self::DEFAULT_FORMAT) : string
    {
        $value = Items::getter($row, $column);

        switch ($column) {
            case "status":
                $value = htmlspecialchars($this->txt("status_" . Log::$status_all[$value]));
                break;

            case "user_id":
            case "execute_user_id":
                $value = htmlspecialchars(ilObjUser::_lookupLogin($value));
                break;

            default:
                break;
        }

        return strval($value);
    }


    /**
     * @inheritDoc
     */
    protected function initCommands() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initData() : void
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setDefaultOrderField("date");
        $this->setDefaultOrderDirection("desc");

        // Fix stupid ilTable2GUI !!! ...
        $this->determineLimit();
        $this->determineOffsetAndOrder();

        $filter = $this->getFilterValues();

        $message = $filter["message"];
        $date_start = $filter["date"]["start"];
        if (!empty($date_start)) {
            $date_start = new ilDateTime(intval($date_start), IL_CAL_UNIX);
        } else {
            $date_start = null;
        }
        $date_end = $filter["date"]["end"];
        if (!empty($date_end)) {
            $date_end = new ilDateTime(intval($date_end), IL_CAL_UNIX);
        } else {
            $date_end = null;
        }
        $status = $filter["status"];
        if (!empty($status)) {
            $status = intval($status);
        } else {
            $status = null;
        }

        $this->setData(self::srUserEnrolment()->logs()
            ->getLogs($this->parent_obj->getObjId(), $this->getOrderField(), $this->getOrderDirection(), intval($this->getOffset()),
                intval($this->getLimit()), $message, $date_start,
                $date_end, $status));

        $this->setMaxCount(self::srUserEnrolment()->logs()->getLogsCount($this->parent_obj->getObjId(), $message, $date_start, $date_end,
            $status));
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields() : void
    {
        self::dic()->language()->loadLanguageModule("form");

        $this->filter_fields = [
            "date"    => [
                PropertyFormGUI::PROPERTY_CLASS => DateDurationInputGUI::class,
                "setShowTime"                   => true
            ],
            "message" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "status"  => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                        "" => ""
                    ] + array_map(function (string $status_lang_key) : string {
                        return $this->txt("status_" . $status_lang_key);
                    }, Log::$status_all)
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {
        $this->setId(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_logs");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {
        $this->setTitle($this->txt("logs"));
    }
}
