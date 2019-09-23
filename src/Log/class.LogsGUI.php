<?php

namespace srag\Plugins\SrUserEnrolment\Log;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class LogsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\Log
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\Log\LogsGUI: srag\Plugins\SrUserEnrolment\Rule\RulesGUI
 */
class LogsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_LIST_LOGS = "listLogs";
    const CMD_RESET_FILTER = "resetFilter";
    const TAB_LOGS = "logs";
    const LANG_MODULE_LOGS = "logs";


    /**
     * LogsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LOGS);

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_LIST_LOGS:
                    case self::CMD_RESET_FILTER:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param string $cmd
     *
     * @return LogsTableGUI
     */
    protected function getLogsTable(string $cmd = self::CMD_LIST_LOGS) : LogsTableGUI
    {
        $table = new LogsTableGUI($this, $cmd);

        return $table;
    }


    /**
     *
     */
    protected function listLogs()/*: void*/
    {
        $table = $this->getLogsTable();

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = $this->getLogsTable(self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_LOGS);
        $this->listLogs(); // Fix reset offset
    }


    /**
     *
     */
    protected function resetFilter()/*: void*/
    {
        $table = $this->getLogsTable(self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_LOGS);
        $this->listLogs(); // Fix reset offset
    }
}
