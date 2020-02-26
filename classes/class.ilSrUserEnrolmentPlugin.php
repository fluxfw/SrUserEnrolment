<?php

require_once __DIR__ . "/../vendor/autoload.php";
if (file_exists(__DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php")) {
    require_once __DIR__ . "/../../../../Cron/CronHook/SrUserEnrolmentCron/vendor/autoload.php";
}

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\DeleteRequests\DeleteRequests;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\Member;
use srag\Plugins\SrUserEnrolment\Menu\Menu;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RemovePluginDataConfirm\SrUserEnrolment\PluginUninstallTrait;

/**
 * Class ilSrUserEnrolmentPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUserEnrolmentPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_ID = "srusrenr";
    const PLUGIN_NAME = "SrUserEnrolment";
    const PLUGIN_CLASS_NAME = self::class;
    const EVENT_AFTER_REQUEST = "after_request";
    const EVENT_COLLECT_ASSISTANTS_REQUESTS_TABLE_MODIFICATIONS = "collect_assistants_requests_table_modifications";
    const EVENT_COLLECT_MEMBERS_TABLE_MODIFICATIONS = "collect_members_table_modifications";
    const EVENT_COLLECT_MEMBER_FORM_MODIFICATIONS = "collect_member_form_modifications";
    const EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS = "collect_requests_table_modifications";
    const EVENT_EXTENDS_SRUSRENR = "extends_" . self::PLUGIN_ID;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * ilSrUserEnrolmentPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function handleEvent(/*string*/ $a_component, /*string*/ $a_event,/*array*/ $a_parameter)/*: void*/
    {
        switch ($a_component) {
            case "Modules/Course":
                switch ($a_event) {
                    case "addParticipant":
                        self::srUserEnrolment()->enrolmentWorkflow()->members()->setEnrollmentTime(current(ilObject::_getAllReferences($a_parameter["obj_id"])), $a_parameter["usr_id"], time());
                        break;

                    case "deleteParticipant":
                        self::srUserEnrolment()->enrolmentWorkflow()->members()->deleteMember(self::srUserEnrolment()
                            ->enrolmentWorkflow()
                            ->members()
                            ->getMember(current(ilObject::_getAllReferences($a_parameter["obj_id"])), $a_parameter["usr_id"]));
                        break;

                    default:
                        break;
                }
                break;

            default:
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/*:void*/
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        self::srUserEnrolment()->comments()->installLanguages();

        self::srUserEnrolment()->notifications4plugin()->installLanguages();

        self::srUserEnrolment()->requiredData()->installLanguages();
    }


    /**
     * @inheritDoc
     */
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider
    {
        return new Menu(self::dic()->dic(), $this);
    }


    /**
     * @inheritDoc
     */
    protected function deleteData()/*: void*/
    {
        self::srUserEnrolment()->dropTables();
    }
}
