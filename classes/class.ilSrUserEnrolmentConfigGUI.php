<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DevTools\SrUserEnrolment\DevToolsCtrl;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl;
use srag\Plugins\SrUserEnrolment\Config\ConfigCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ilSrUserEnrolmentConfigGUI
 *
 * @ilCtrl_isCalledBy srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl: ilSrUserEnrolmentConfigGUI
 * @ilCtrl_isCalledBy srag\DevTools\SrUserEnrolment\DevToolsCtrl: ilSrUserEnrolmentConfigGUI
 */
class ilSrUserEnrolmentConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_CONFIGURE = "configure";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * ilSrUserEnrolmentConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd) : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ConfigCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ConfigCtrl());
                break;

            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
                break;

            case strtolower(ExcelImportGUI::class):
                self::dic()->ctrl()->forwardCommand(new ExcelImportGUI());
                break;

            case strtolower(WorkflowsGUI::class):
                if (!self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId(), false)) {
                    die();
                }
                self::dic()->ctrl()->forwardCommand(new WorkflowsGUI());
                break;

            case strtolower(NotificationsCtrl::class):
                if (!self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId(), false)) {
                    die();
                }
                self::dic()->tabs()->activateTab(NotificationsCtrl::TAB_NOTIFICATIONS);
                self::dic()->ctrl()->forwardCommand(new NotificationsCtrl());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
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
    protected function configure() : void
    {
        self::dic()->ctrl()->redirectByClass(ConfigCtrl::class, ConfigCtrl::CMD_CONFIGURE);
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        ConfigCtrl::addTabs();

        WorkflowsGUI::addTabs();

        DevToolsCtrl::addTabs(self::plugin());

        self::dic()->locator()->addItem(ilSrUserEnrolmentPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }
}
