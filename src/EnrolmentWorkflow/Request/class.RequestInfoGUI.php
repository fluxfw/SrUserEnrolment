<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilDashboardGUI;
use ilObject;
use ilPersonalDesktopGUI;
use ilSrUserEnrolmentPlugin;
use ilSrUserEnrolmentUIHookGUI;
use ilSubmitButton;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\CustomInputGUIs\SrUserEnrolment\Template\Template;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Comment\RequestCommentsCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestInfoGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI: ilUIPluginRouterGUI
 */
class RequestInfoGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const CMD_ADD_RESPONSIBLE_USERS = "addResponsibleUsers";
    const CMD_BACK = "back";
    const CMD_CREATE_RESPONSIBLE_USERS = "createResponsibleUsers";
    const CMD_SHOW_WORKFLOW = "showWorkflow";
    const GET_PARAM_REQUEST_ID = "request_id";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const TAB_ADD_RESPONSIBLE_USERS = "add_responsible_users";
    const TAB_WORKFLOW = "workflow";
    /**
     * @var int|null
     */
    protected $obj_ref_id = null;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var bool
     */
    protected $single = true;


    /**
     * RequestInfoGUI constructor
     *
     * @param bool $single
     */
    public function __construct(bool $single = true)
    {
        $this->single = $single;
    }


    /**
     * @return array
     */
    public static function addRequestsToPersonalDesktop() : array
    {
        $requests = self::srUserEnrolment()
            ->enrolmentWorkflow()
            ->requests()
            ->getRequests(null, null, [self::dic()->user()->getId()], true, null, null, null, null, [RequestGroup::EDITED_STATUS_NOT_EDITED, RequestGroup::EDITED_STATUS_IN_EDITING]);

        if (!empty($requests)) {
            $tpl = self::plugin()->template("EnrolmentWorkflow/pd_my_requests.html");

            $tpl->setVariableEscaped("MY_REQUESTS_TITLE", self::plugin()->translate("my_requests", RequestsGUI::LANG_MODULE));

            foreach ($requests as $request
            ) {
                /**
                 * @var Request $request
                 */

                $tpl->setVariable("LINK", $request->getRequestLink());

                $tpl->setVariableEscaped("OBJECT_TITLE", self::dic()->objDataCache()->lookupTitle($request->getObjId()));

                $tpl->setVariable("OBJECT_ICON", self::output()->getHTML(self::dic()->ui()->factory()->image()->standard(ilObject::_getIcon($request->getObjId()), "")));

                $tpl->setVariableEscaped("CURRENT_STEP", self::plugin()->translate("step", StepsGUI::LANG_MODULE) . ": " . $request->getStep()->getTitle());

                $tpl->parseCurrentBlock();
            }

            return ["mode" => ilSrUserEnrolmentUIHookGUI::APPEND, "html" => self::output()->getHTML($tpl)];
        }

        return ["mode" => ilSrUserEnrolmentUIHookGUI::KEEP, "html" => ""];
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, RequestsGUI::GET_PARAM_REF_ID));

        $this->request = self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequestById(filter_input(INPUT_GET, self::GET_PARAM_REQUEST_ID));

        if ($this->request === null || (!empty($this->obj_ref_id) ? $this->request->getObjRefId() !== $this->obj_ref_id : false)
            || ($this->single ? $this->request->getUserId() !== intval(self::dic()
                    ->user()
                    ->getId()) : false)
        ) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REQUEST_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(EditRequestGUI::class):
                if (!$this->single) {
                    self::dic()->ctrl()->forwardCommand(new EditRequestGUI($this));
                }
                break;

            case strtolower(RequestCommentsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new RequestCommentsCtrl($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_RESPONSIBLE_USERS:
                    case self::CMD_CREATE_RESPONSIBLE_USERS:
                    case self::CMD_BACK:
                    case self::CMD_SHOW_WORKFLOW:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }


    /**
     * @return bool
     */
    public function isSingle() : bool
    {
        return $this->single;
    }


    /**
     *
     */
    protected function addResponsibleUsers() : void
    {
        if (!(!$this->single && !empty(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($this->request, self::dic()->user()->getId())))) {
            die();
        }

        self::dic()->tabs()->activateTab(self::TAB_ADD_RESPONSIBLE_USERS);

        self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $users = new MultiSelectSearchNewInputGUI("", "responsible_" . RequestStepGUI::GET_PARAM_USER_ID);
        $users->setAjaxAutoCompleteCtrl(new UsersAjaxAutoCompleteCtrl());
        self::dic()->toolbar()->addInputItem($users);

        $add_responsible_users_button = ilSubmitButton::getInstance();
        $add_responsible_users_button->setCaption(self::plugin()->translate("add_responsible_users", RequestsGUI::LANG_MODULE), false);
        $add_responsible_users_button->setCommand(self::CMD_CREATE_RESPONSIBLE_USERS);
        self::dic()->toolbar()->addButtonInstance($add_responsible_users_button);

        self::output()->output("", true);
    }


    /**
     *
     */
    protected function back() : void
    {
        if ($this->single) {
            self::dic()->ctrl()->redirectByClass(ilDashboardGUI::class);
        } else {
            self::dic()->ctrl()->redirectByClass(RequestsGUI::class, RequestsGUI::CMD_LIST_REQUESTS);
        }
    }


    /**
     *
     */
    protected function createResponsibleUsers() : void
    {
        if (!$this->single && !empty(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($this->request, self::dic()->user()->getId()))) {
            $user_ids = filter_input(INPUT_POST, "responsible_" . RequestStepGUI::GET_PARAM_USER_ID, FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (!is_array($user_ids)) {
                $user_ids = [];
            }

            $user_ids = MultiSelectSearchNewInputGUI::cleanValues($user_ids);

            foreach ($user_ids as $user_id) {
                if ($this->request->getUserId() !== intval($user_id)) {
                    $this->request->addResponsibleUser($user_id);
                }
            }

            self::srUserEnrolment()->enrolmentWorkflow()->requests()->storeRequest($this->request);
        }

        ilUtil::sendSuccess(self::plugin()->translate("added_responsible_users", RequestsGUI::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_ADD_RESPONSIBLE_USERS);
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->ui()->mainTemplate()->setTitleIcon(ilObject::_getIcon("", "tiny",
            self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($this->request->getObjRefId()))));

        self::dic()->ui()->mainTemplate()->setTitle(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->request->getObjRefId())));

        if ($this->single) {
            self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("personal_desktop"), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK));
        } else {
            self::dic()->tabs()->setBackTarget(self::plugin()->translate("requests", RequestsGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK));
        }

        self::dic()->tabs()->addTab(self::TAB_WORKFLOW, $this->request->getWorkflow()->getTitle(), $this->request->getRequestLink(!empty($this->obj_ref_id)));

        if (!$this->single && !empty(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($this->request, self::dic()->user()->getId()))) {

            self::dic()
                ->tabs()
                ->addTab(self::TAB_ADD_RESPONSIBLE_USERS, self::plugin()->translate("add_responsible_users", RequestsGUI::LANG_MODULE),
                    self::dic()->ctrl()->getLinkTarget($this, self::CMD_ADD_RESPONSIBLE_USERS));
        }
    }


    /**
     *
     */
    protected function showWorkflow() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_WORKFLOW);

        $workflow_list = '';

        foreach (self::srUserEnrolment()->enrolmentWorkflow()->requests()->getRequests($this->request->getObjRefId(), null, [$this->request->getUserId()], false) as $request) {

            $text = htmlspecialchars($request->getStep()->getTitle());
            $info = [];

            if ($request->isEdited()) {
                $info = [
                    $request->getFormattedEditedTime(),
                    $request->getEditedUser()->getFullname()
                ];
            }

            if ($request->getRequestId() === $this->request->getRequestId()) {
                $text = '<b>' . $text . '</b>';
            }

            $text = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($text, $request->getRequestLink(!empty($this->obj_ref_id))));

            $info_tpl = new Template(__DIR__ . "/../../../vendor/srag/custominputguis/src/PropertyFormGUI/Items/templates/input_gui_input_info.html", true, true);
            $info_tpl->setVariable("INFO", nl2br(implode("\n", array_map(function (string $info) : string {
                return htmlspecialchars($info);
            }, $info))));

            $workflow_list .= '<div>' . self::output()->getHTML([$text, $info_tpl]) . '</div>';
        }

        if (!$this->single) {
            $actions = [];

            foreach (self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($this->request, self::dic()->user()->getId()) as $step) {
                self::dic()->ctrl()->setParameterByClass(EditRequestGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());

                $actions[] = self::dic()->ui()->factory()->link()->standard($step->getActionEditTitle(), self::dic()->ctrl()
                    ->getLinkTargetByClass(EditRequestGUI::class, EditRequestGUI::CMD_CONFIRM_EDIT_REQUEST));
            }

            self::dic()->ui()->mainTemplate()->setHeaderActionMenu(self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel(self::plugin()
                ->translate("actions", RequestsGUI::LANG_MODULE))));
        }

        self::dic()->ui()->mainTemplate()->setRightContent(self::output()->getHTML(self::srUserEnrolment()->commentsUI()->withPlugin(self::plugin())->withCtrlClass(new RequestCommentsCtrl($this))));

        $required_data = $this->request->getFormattedRequiredData();

        self::output()->output([
            $workflow_list,
            "<br><br>",
            self::dic()->ui()->factory()->listing()->descriptive(array_combine(array_map("htmlspecialchars", array_keys($required_data)),
                array_map("htmlspecialchars", $required_data)))
        ], true);
    }
}
