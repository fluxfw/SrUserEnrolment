<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilLink;
use ilSession;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestStepForOthersGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepForOthersGUI: ilUIPluginRouterGUI
 */
class RequestStepForOthersGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_BACK = "back";
    const CMD_LIST_USERS = "listUsers";
    const CMD_RESET_FILTER = "resetFilter";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_USERS = "users";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var int
     */
    protected $parent_ref_id;
    /**
     * @var Step
     */
    protected $step;


    /**
     * RequestStepForOthersGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, RequestsGUI::GET_PARAM_REF_ID));
        $this->step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById(intval(filter_input(INPUT_GET, StepGUI::GET_PARAM_STEP_ID)));
        $this->parent_ref_id = intval(filter_input(INPUT_GET, RequestStepGUI::GET_PARAM_PARENT_REF_ID));

        if (!self::srUserEnrolment()->enrolmentWorkflow()->assistants()->hasAccess(self::dic()->user()->getId())) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, RequestsGUI::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, StepGUI::GET_PARAM_STEP_ID);
        self::dic()->ctrl()->saveParameter($this, RequestStepGUI::GET_PARAM_PARENT_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_BACK:
                    case self::CMD_LIST_USERS:
                    case RequestStepGUI::CMD_REQUEST_STEP:
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
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @return Step
     */
    public function getStep() : Step
    {
        return $this->step;
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newRequestStepForOthersTableInstance($this, self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_USERS);
        $this->listUsers(); // Fix reset offset
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        if (!empty($this->parent_ref_id)) {
            self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->parent_ref_id));
        } else {
            self::dic()->ctrl()->redirectToURL(ilLink::_getLink(self::dic()->repositoryTree()->getParentId($this->obj_ref_id)));
        }
    }


    /**
     *
     */
    protected function listUsers()/*:void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_USERS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newRequestStepForOthersTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function requestStep()/*: void*/
    {
        ilSession::set(RequestStepGUI::SESSION_USERS, filter_input(INPUT_POST, RequestStepGUI::GET_PARAM_USER_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY));

        self::dic()->ctrl()->saveParameterByClass(RequestStepGUI::class, RequestsGUI::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameterByClass(RequestStepGUI::class, StepGUI::GET_PARAM_STEP_ID);
        self::dic()->ctrl()->saveParameterByClass(RequestStepGUI::class, RequestStepGUI::GET_PARAM_PARENT_REF_ID);

        self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP);
    }


    /**
     *
     */
    protected function resetFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->requests()->factory()->newRequestStepForOthersTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_USERS);
        $this->listUsers(); // Fix reset offset
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", AssistantsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_USERS, self::plugin()->translate("users", AssistantsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_LIST_USERS));
    }
}
