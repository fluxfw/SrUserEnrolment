<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ILIAS\UI\Component\Component;
use ilLink;
use ilSession;
use ilSrUserEnrolmentPlugin;
use ilSrUserEnrolmentUIHookGUI;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestStepGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI: ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI
 */
class RequestStepGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_BACK = "back";
    const CMD_REQUEST_STEP = "requestStep";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const GET_PARAM_USER_ID = "user_id";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const SESSION_USERS = ilSrUserEnrolmentPlugin::PLUGIN_ID . "_users";
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
     * @var int[]
     */
    protected $user_ids;


    /**
     * RequestStepGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param array $a_par
     *
     * @return array
     */
    public static function addObjectActions(array $a_par) : array
    {
        $html = $a_par["html"];

        $matches = [];
        preg_match('/id="act_([0-9]+)/', $html, $matches);
        if (is_array($matches) && count($matches) >= 2) {

            $obj_ref_id = intval($matches[1]);

            self::dic()->ctrl()->setParameterByClass(self::class, RequestsGUI::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()->ctrl()->setParameterByClass(RequestStepForOthersGUI::class, RequestsGUI::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->ctrl()->saveParameterByClass(self::class, self::GET_PARAM_PARENT_REF_ID);
            self::dic()->ctrl()->saveParameterByClass(RequestStepForOthersGUI::class, self::GET_PARAM_PARENT_REF_ID);

            $actions = [];
            foreach (
                self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForRequest(AbstractRule::TYPE_STEP_ACTION, self::dic()->user()->getId(), self::dic()->user()->getId(), $obj_ref_id) as
                $step
            ) {
                self::dic()->ctrl()->setParameterByClass(self::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());
                self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_USER_ID, self::dic()->user()->getId());
                if (!is_array($actions[$step->getStepId()])) {
                    $actions[$step->getStepId()] = [];
                }
                $actions[$step->getStepId()][] = self::dic()->ui()->factory()->link()->standard('<span class="xsmall">' . $step->getActionTitle() . '</span>',
                    self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_REQUEST_STEP));
            }

            foreach (self::srUserEnrolment()->enrolmentWorkflow()->requests()->getPossibleUsersForRequestStepForOthers(self::dic()->user()->getId()) as $user) {
                foreach (
                    self::srUserEnrolment()
                        ->enrolmentWorkflow()
                        ->steps()
                        ->getStepsForRequest(AbstractRule::TYPE_STEP_ACTION, $user->getId(), $user->getId(), $obj_ref_id) as $step
                ) {
                    self::dic()->ctrl()->setParameterByClass(RequestStepForOthersGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());
                    if (!is_array($actions[$step->getStepId()])) {
                        $actions[$step->getStepId()] = [];
                    }
                    $actions[$step->getStepId()][] = self::dic()->ui()->factory()->link()->standard('<span class="xsmall">' .
                        self::plugin()->translate("step_action", AssistantsGUI::LANG_MODULE, [
                            $step->getActionTitle()
                        ]) . '</span>',
                        self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, RequestStepForOthersGUI::class], RequestStepForOthersGUI::CMD_LIST_USERS));
                }
            }

            if (!empty($actions)) {
                $actions_html = self::output()->getHTML(array_map(function (Component $action) : string {
                    return '<li>' . self::output()->getHTML($action) . '</li>';
                }, array_reduce($actions, function (array $actions, array $actions2) : array {
                    $actions = array_merge($actions, [current($actions2)]);

                    return $actions;
                }, [])));

                $matches = [];
                preg_match('/<ul\s+class="dropdown-menu pull-right"\s+role="menu"\s+id="ilAdvSelListTable_.*"\s*>/',
                    $html, $matches);
                if (is_array($matches) && count($matches) >= 1) {
                    $html = str_ireplace($matches[0], $matches[0] . $actions_html, $html);
                } else {
                    $html = $actions_html . $html;
                }

                return ["mode" => ilSrUserEnrolmentUIHookGUI::REPLACE, "html" => $html];
            }
        }

        return ["mode" => ilSrUserEnrolmentUIHookGUI::KEEP, "html" => ""];
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, RequestsGUI::GET_PARAM_REF_ID));
        $this->step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById(intval(filter_input(INPUT_GET, StepGUI::GET_PARAM_STEP_ID)));
        $this->parent_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_REF_ID));

        if (strtolower(self::dic()->http()->request()->getMethod()) === "post") {
            $this->user_ids = filter_input(INPUT_POST, self::GET_PARAM_USER_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (is_array($this->user_ids)) {
                $this->user_ids = MultiSelectSearchNewInputGUI::cleanValues($this->user_ids);
            }
        } else {
            $this->user_ids = [intval(filter_input(INPUT_GET, self::GET_PARAM_USER_ID))];

            if (empty($this->user_ids[0])) {
                $this->user_ids = ilSession::get(self::SESSION_USERS);
            }
        }

        self::dic()->ctrl()->saveParameter($this, RequestsGUI::GET_PARAM_REF_ID);
        self::dic()->ctrl()->saveParameter($this, StepGUI::GET_PARAM_STEP_ID);
        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_PARENT_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(FillCtrl::class):
                self::dic()->ctrl()->forwardCommand(new FillCtrl(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId(), FillCtrl::RETURN_REQUEST_STEP));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_REQUEST_STEP:
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
    protected function back()/*: void*/
    {
        ilSession::clear(self::SESSION_USERS);
        self::srUserEnrolment()->requiredData()->fills()->clearTempFillValues();

        if (!empty($this->parent_ref_id)) {
            self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->parent_ref_id));
        } else {
            self::dic()->ctrl()->redirectToURL(ilLink::_getLink(self::dic()->repositoryTree()->getParentId($this->obj_ref_id)));
        }
    }


    /**
     *
     */
    protected function requestStep()/*: void*/
    {
        if (!is_array($this->user_ids) || empty($this->user_ids)) {
            self::dic()->ctrl()->redirect($this, self::CMD_BACK);

            return;
        }

        $required_data_fields = self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId());

        if (!empty($required_data_fields)) {
            $required_data = self::srUserEnrolment()->requiredData()->fills()->getFillValues();

            if (empty($required_data)) {
                ilSession::set(self::SESSION_USERS, $this->user_ids);

                self::dic()->ctrl()->redirectByClass([FillCtrl::class], FillCtrl::CMD_FILL_FIELDS);

                return;
            }
        } else {
            $required_data = null;
        }

        $this->user_ids = array_filter($this->user_ids, function (int $user_id) : bool {
            return self::srUserEnrolment()->enrolmentWorkflow()->requests()->canRequestWithAssistant($this->obj_ref_id, $this->step->getStepId(), self::dic()->user()->getId(), $user_id);
        });

        foreach ($this->user_ids as $user_id) {
            self::srUserEnrolment()->enrolmentWorkflow()->requests()->request($this->obj_ref_id, $this->step->getStepId(), $user_id, $required_data);
        }

        ilUtil::sendSuccess(self::plugin()->translate("requested", RequestsGUI::LANG_MODULE, [$this->step->getActionTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
