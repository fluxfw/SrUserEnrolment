<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class ilSrUserEnrolmentConfigGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationsCtrl: ilSrUserEnrolmentConfigGUI
 */
class ilSrUserEnrolmentConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const LANG_MODULE = "config";
    const TAB_CONFIGURATION = "configuration";


    /**
     * ilSrUserEnrolmentConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ExcelImportGUI::class);
                self::dic()->ctrl()->forwardCommand(new ExcelImportGUI());
                break;

            case strtolower(WorkflowsGUI::class);
                if (!self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId(), false)) {
                    die();
                }
                self::dic()->ctrl()->forwardCommand(new WorkflowsGUI());
                break;

            case strtolower(NotificationsCtrl::class);
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
                    case self::CMD_UPDATE_CONFIGURE:
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
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_CONFIGURATION, self::plugin()->translate("configuration", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_CONFIGURE));

        WorkflowsGUI::addTabs();

        self::dic()->locator()->addItem(ilSrUserEnrolmentPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::srUserEnrolment()->config()->factory()->newFormInstance($this);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function updateConfigure()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::srUserEnrolment()->config()->factory()->newFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("configuration_saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
    }
}
