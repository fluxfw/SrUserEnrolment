<?php

namespace srag\Plugins\SrUserEnrolment\RuleEnrolment\Log;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class LogsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\RuleEnrolment\Log
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\RuleEnrolment\Log\LogsGUI: srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI
 */
class LogsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_LIST_LOGS = "listLogs";
    const CMD_RESET_FILTER = "resetFilter";
    const LANG_MODULE = "logs";
    const TAB_LOGS = "logs";
    /**
     * @var int
     */
    protected $obj_id;


    /**
     * LogsGUI constructor
     *
     * @param int $obj_id
     */
    public function __construct(int $obj_id)
    {
        $this->obj_id = $obj_id;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

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
     *
     */
    public static function addTabs()/*:void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LOGS, self::plugin()->translate("logs", LogsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_LOGS));
    }


    /**
     *
     */
    protected function setTabs()/*:void*/
    {

    }


    /**
     *
     */
    protected function listLogs()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LOGS);

        $table = self::srUserEnrolment()->ruleEnrolment()->logs()->factory()->newTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->ruleEnrolment()->logs()->factory()->newTableInstance($this, self::CMD_APPLY_FILTER);

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
        $table = self::srUserEnrolment()->ruleEnrolment()->logs()->factory()->newTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_LOGS);
        $this->listLogs(); // Fix reset offset
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }
}
