<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\CustomInputGUIs\SrUserEnrolment\Loader\CustomInputGUIsLoaderDetector;
use srag\DevTools\SrUserEnrolment\DevToolsCtrl;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RemovePluginDataConfirm\SrUserEnrolment\PluginUninstallTrait;

/**
 * Class ilSrUserEnrolmentPlugin
 */
class ilSrUserEnrolmentPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrUserEnrolmentTrait;

    const EVENT_AFTER_REQUEST = "after_request";
    const EVENT_COLLECT_MEMBERS_TABLE_MODIFICATIONS = "collect_members_table_modifications";
    const EVENT_COLLECT_MEMBER_FORM_MODIFICATIONS = "collect_member_form_modifications";
    const EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS = "collect_requests_table_modifications";
    const EVENT_COLLECT_REQUEST_STEP_FOR_OTHERS_TABLE_MODIFICATIONS = "collect_request_step_for_others_table_modifications";
    const EVENT_EXTENDS_SRUSRENR = "extends_" . self::PLUGIN_ID;
    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "srusrenr";
    const PLUGIN_NAME = "SrUserEnrolment";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilSrUserEnrolmentPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


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
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
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
    public function handleEvent(/*string*/ $a_component, /*string*/ $a_event,/*array*/ $a_parameter) : void
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

            case "Services/User":
                switch ($a_event) {
                    case "deleteUser":
                        self::srUserEnrolment()->deleteByUser($a_parameter["usr_id"]);
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
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider
    {
        return self::srUserEnrolment()->menu();
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null) : void
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        self::srUserEnrolment()->comments()->installLanguages();

        self::srUserEnrolment()->notifications4plugin()->installLanguages();

        self::srUserEnrolment()->requiredData()->installLanguages();

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData() : void
    {
        self::srUserEnrolment()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return true;
    }
}
