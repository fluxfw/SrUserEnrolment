<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ilLink;
use ilSession;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AssistantsRequestGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsRequestGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsRequestGUI
 */
class AssistantsRequestGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_BACK = "back";
    const CMD_LIST_USERS = "listUsers";
    const CMD_MULTIPLE_REQUESTS = "multipleRequests";
    const CMD_RESET_FILTER = "resetFilter";
    const SESSION_USERS = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_users";
    const TAB_USERS = "users";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var Step
     */
    protected $step;


    /**
     * AssistantsRequestGUI constructor
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

        if (!self::srUserEnrolment()->enrolmentWorkflow()->assistants()->hasAccess(self::dic()->user()->getId())) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, RequestsGUI::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, StepGUI::GET_PARAM_STEP_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(FillCtrl::class):
                self::dic()->ctrl()->forwardCommand(new FillCtrl(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId(), FillCtrl::RETURN_MULTIPLE_REQUESTS));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_BACK:
                    case self::CMD_LIST_USERS:
                    case self::CMD_MULTIPLE_REQUESTS:
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
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("back", AssistantsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_USERS, self::plugin()->translate("users", AssistantsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_USERS));
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        ilSession::clear(self::SESSION_USERS);
        self::srUserEnrolment()->requiredData()->fills()->clearTempFillValues();

        self::dic()->ctrl()->redirectToURL(ilLink::_getLink(self::dic()->tree()->getParentId($this->obj_ref_id)));
    }


    /**
     *
     */
    protected function listUsers()/*:void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_USERS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newRequestsTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newRequestsTableInstance($this, self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_USERS);
        $this->listUsers(); // Fix reset offset
    }


    /**
     *
     */
    protected function resetFilter()/*: void*/
    {
        $table = self::srUserEnrolment()->enrolmentWorkflow()->assistants()->factory()->newRequestsTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this, self::CMD_LIST_USERS);
        $this->listUsers(); // Fix reset offset
    }


    /**
     *
     */
    protected function multipleRequests()/*: void*/
    {
        $obj_ref_id = intval(filter_input(INPUT_GET, RequestsGUI::GET_PARAM_REF_ID));
        $step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById(intval(filter_input(INPUT_GET, StepGUI::GET_PARAM_STEP_ID)));

        if (strtolower(self::dic()->http()->request()->getMethod()) === "post") {
            $user_ids = filter_input(INPUT_POST, RequestStepGUI::GET_PARAM_USER_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        } else {
            $user_ids = ilSession::get(self::SESSION_USERS);
        }

        if (!is_array($user_ids) || empty($user_ids)) {
            self::dic()->ctrl()->redirect($this, self::CMD_BACK);

            return;
        }

        $required_data_fields = self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $step->getStepId());

        if (!empty($required_data_fields)) {
            $required_data = self::srUserEnrolment()->requiredData()->fills()->getFillValues();

            if (empty($required_data)) {
                ilSession::set(self::SESSION_USERS, $user_ids);

                self::dic()->ctrl()->redirectByClass([FillCtrl::class], FillCtrl::CMD_FILL_FIELDS);

                return;
            }
        } else {
            $required_data = null;
        }

        $user_ids = array_filter($user_ids, function (int $user_id) use ($obj_ref_id, $step): bool {
            return self::srUserEnrolment()->enrolmentWorkflow()->requests()->canRequestWithAssistant($obj_ref_id, $step->getStepId(), self::dic()->user()->getId(), $user_id);
        });

        foreach ($user_ids as $user_id) {
            self::srUserEnrolment()->enrolmentWorkflow()->requests()->request($obj_ref_id, $step->getStepId(), $user_id, $required_data);
        }

        ilUtil::sendSuccess(self::plugin()->translate("requested", RequestsGUI::LANG_MODULE, [$step->getActionTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
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
}
