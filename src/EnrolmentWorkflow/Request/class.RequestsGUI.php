<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrUserEnrolmentPlugin;
use ilSubmitButton;
use ilUIPluginRouterGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchInputGUI\MultiSelectSearchInputGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
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
 */
class RequestsGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_BACK = "back";
    const CMD_GET_USERS_AUTO_COMPLETE = "getUsersAutoComplete";
    const CMD_GET_USERS_AUTO_COMPLETE_REQUEST = "getUsersAutoCompleteRequest";
    const CMD_LIST_REQUESTS = "listRequests";
    const CMD_RESET_FILTER = "resetFilter";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "requests";
    const TAB_REQUESTS = "requests";
    /**
     * @var int|null
     */
    protected $obj_ref_id = null;


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

        if (!self::srUserEnrolment()->enrolmentWorkflow()->requests()->hasAccess(self::dic()->user()->getId())) {
            die("");
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(RequestInfoGUI::class):
                self::dic()->ctrl()->forwardCommand(new RequestInfoGUI(false));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_BACK:
                    case self::CMD_GET_USERS_AUTO_COMPLETE:
                    case self::CMD_GET_USERS_AUTO_COMPLETE_REQUEST:
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

            self::dic()->tabs()->addTab(self::TAB_REQUESTS, self::plugin()->translate("requests", self::LANG_MODULE), self::dic()->ctrl()
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

            self::dic()->ctrl()->saveParameterByClass(RequestStepGUI::class, self::GET_PARAM_REF_ID);
            $step = current(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                ->enrolmentWorkflow()
                ->selectedWorkflows()
                ->getWorkflowId(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))));
            self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());
            self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getFormActionByClass(RequestStepGUI::class));

            $users = new MultiSelectSearchInputGUI("", RequestStepGUI::GET_PARAM_USER_ID);
            $users->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this, self::CMD_GET_USERS_AUTO_COMPLETE_REQUEST, "", true, false));
            self::dic()->toolbar()->addInputItem($users);

            $request_button = ilSubmitButton::getInstance();
            $request_button->setCaption($step->getActionTitle(), false);
            $request_button->setCommand(RequestStepGUI::CMD_REQUEST_STEP);
            self::dic()->toolbar()->addButtonInstance($request_button);
        }

        self::dic()->tabs()->addTab(self::TAB_REQUESTS, self::plugin()->translate("requests", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_REQUESTS));
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->saveParameterByClass(ilObjCourseGUI::class, self::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectByClass([
            ilRepositoryGUI::class,
            ilObjCourseGUI::class
        ]);
    }


    /**
     *
     */
    protected function listRequests()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_REQUESTS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function getUsersAutoComplete()/*: void*/
    {
        $search = strval(filter_input(INPUT_GET, "term", FILTER_DEFAULT, FILTER_FORCE_ARRAY)["term"]);

        $options = [];

        foreach (self::srUserEnrolment()->ruleEnrolment()->searchUsers($search) as $id => $title) {
            $options[] = [
                "id"   => $id,
                "text" => $title
            ];
        }

        self::output()->outputJSON(["result" => $options]);
    }


    /**
     *
     */
    protected function getUsersAutoCompleteRequest()/*: void*/
    {
        $search = strval(filter_input(INPUT_GET, "term", FILTER_DEFAULT, FILTER_FORCE_ARRAY)["term"]);

        $options = [];

        foreach (self::srUserEnrolment()->ruleEnrolment()->searchUsers($search) as $id => $title) {
            if (self::srUserEnrolment()
                ->enrolmentWorkflow()
                ->requests()
                ->canRequestWithAssistant($this->obj_ref_id, current(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                    ->enrolmentWorkflow()
                    ->selectedWorkflows()
                    ->getWorkflowId(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id))))->getStepId(), self::dic()->user()->getId(), $id)
            ) {
                $options[] = [
                    "id"   => $id,
                    "text" => $title
                ];
            }
        }

        self::output()->outputJSON(["result" => $options]);
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
}
