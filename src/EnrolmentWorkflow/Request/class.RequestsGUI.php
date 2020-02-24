<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilLink;
use ilSrUserEnrolmentPlugin;
use ilSubmitButton;
use ilUIPluginRouterGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestsGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\UsersAssistantsAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI
 */
class RequestsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_BACK = "back";
    const CMD_LIST_REQUESTS = "listRequests";
    const CMD_RESET_FILTER = "resetFilter";
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_REQUESTS_TYPE = "requests_type";
    const LANG_MODULE = "requests";
    const REQUESTS_TYPE_ALL = 1;
    const REQUESTS_TYPE_OWN = 2;
    const REQUESTS_TYPE_OPEN = 3;
    const REQUESTS_TYPES
        = [
            self::REQUESTS_TYPE_ALL  => "all",
            self::REQUESTS_TYPE_OWN  => "own",
            self::REQUESTS_TYPE_OPEN => "open"
        ];
    const TAB_REQUESTS = "requests_";
    /**
     * @var int|null
     */
    protected $obj_ref_id = null;
    /**
     * @var int
     */
    protected $requests_type;


    /**
     * RequestsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));
        $this->requests_type = intval(filter_input(INPUT_GET, self::GET_PARAM_REQUESTS_TYPE));

        if (!self::srUserEnrolment()->enrolmentWorkflow()->requests()->hasAccess(self::dic()->user()->getId())) {
            die("");
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REQUESTS_TYPE);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RequestInfoGUI::class):
                self::dic()->ctrl()->forwardCommand(new RequestInfoGUI(false));
                break;

            case strtolower(UsersAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UsersAjaxAutoCompleteCtrl());
                break;

            case strtolower(UsersAssistantsAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UsersAssistantsAjaxAutoCompleteCtrl($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_BACK:
                    case self::CMD_LIST_REQUESTS:
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
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->requests()->hasAccess(self::dic()->user()->getId())) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REQUESTS_TYPE, self::REQUESTS_TYPE_ALL);

            self::dic()->tabs()->addTab(self::TAB_REQUESTS . self::REQUESTS_TYPE_ALL, self::plugin()->translate("requests", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_REQUESTS));
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        if (!empty($this->obj_ref_id)) {
            self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK));

            MembersGUI::addTabs($this->obj_ref_id);

            SelectWorkflowGUI::addTabs($this->obj_ref_id);

            self::dic()->ctrl()->saveParameterByClass(RequestStepGUI::class, self::GET_PARAM_REF_ID);
            $step = current(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                ->enrolmentWorkflow()
                ->selectedWorkflows()
                ->getWorkflowId(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))));
            self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());
            self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getFormActionByClass(RequestStepGUI::class));

            $users = new MultiSelectSearchNewInputGUI("", RequestStepGUI::GET_PARAM_USER_ID);
            $users->setAjaxAutoCompleteCtrl(new UsersAssistantsAjaxAutoCompleteCtrl($this));
            self::dic()->toolbar()->addInputItem($users);

            $request_button = ilSubmitButton::getInstance();
            $request_button->setCaption($step->getActionTitle(), false);
            $request_button->setCommand(RequestStepGUI::CMD_REQUEST_STEP);
            self::dic()->toolbar()->addButtonInstance($request_button);
        }

        self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REQUESTS_TYPE, self::REQUESTS_TYPE_ALL);
        self::dic()->tabs()->addTab(self::TAB_REQUESTS . self::REQUESTS_TYPE_ALL, self::plugin()->translate("requests", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_REQUESTS));

        foreach (self::REQUESTS_TYPES as $requests_type => $requests_type_lang_key) {
            self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REQUESTS_TYPE, $requests_type);
            self::dic()->tabs()->addSubTab(self::TAB_REQUESTS . $requests_type, self::plugin()->translate("type_" . $requests_type_lang_key, self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_LIST_REQUESTS));
        }
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REQUESTS_TYPE, $this->requests_type);
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->obj_ref_id));
    }


    /**
     *
     */
    protected function listRequests()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_REQUESTS . self::REQUESTS_TYPE_ALL);
        self::dic()->tabs()->activateSubTab(self::TAB_REQUESTS . $this->requests_type);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newTableInstance($this, self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_REQUESTS);
        $this->listRequests(); // Fix reset offset
    }


    /**
     *
     */
    protected function resetFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_REQUESTS);
        $this->listRequests(); // Fix reset offset
    }


    /**
     * @return int|null
     */
    public function getObjRefId()/* : ?int*/
    {
        return $this->obj_ref_id;
    }


    /**
     * @return int
     */
    public function getRequestsType() : int
    {
        return $this->requests_type;
    }
}
