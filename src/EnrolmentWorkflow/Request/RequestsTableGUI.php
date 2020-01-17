<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilObjUser;
use ilSelectInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchInputGUI\MultiSelectSearchInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\Workflow;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RequestsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = RequestsGUI::LANG_MODULE;
    /**
     * @var AbstractRequestsTableModifications[]
     */
    protected $modifications = [];


    /**
     * RequestsTableGUI constructor
     *
     * @param RequestsGUI $parent
     * @param string      $parent_cmd
     */
    public function __construct(RequestsGUI $parent, string $parent_cmd)
    {
        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS, [
            "modifications" => &$this->modifications
        ]);

        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param Request $request
     */
    protected function getColumnValue(/*string*/ $column, /*Request*/ $request, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        foreach ($this->modifications as $modification) {
            $column_value = $modification->formatColumnValue($column, $request);
            if ($column_value !== null) {
                return $column_value;
            }
        }

        switch ($column) {
            case "object_title":
                $column = $request->getObject()->getTitle();
                break;

            case "workflow_title":
                $column = $request->getWorkflow()->getTitle();
                $column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, self::dic()->ctrl()
                    ->getLinkTargetByClass(RequestInfoGUI::class, RequestInfoGUI::CMD_SHOW_WORKFLOW)));
                break;

            case "step_title":
                $column = $request->getStep()->getTitle();
                break;

            case "accepted":
                if ($request->isAccepted()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            case "user_lastname":
                $column = $request->getUser()->getLastname();
                break;

            case "user_firstname":
                $column = $request->getUser()->getFirstname();
                break;

            case "user_email":
                $column = $request->getUser()->getEmail();
                break;

            case "responsible_users":
                $column = nl2br(implode("\n", array_map(function (ilObjUser $responsible_user) : string {
                    return $responsible_user->getFullname();
                }, $request->getFormattedResponsibleUsers())), false);
                break;

            default:
                $column = Items::getter($request, $column);
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "object_title"      => [
                "id"      => "object_title",
                "default" => true,
                "sort"    => false
            ],
            "workflow_title"    => [
                "id"      => "workflow_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("workflow", WorkflowsGUI::LANG_MODULE)
            ],
            "step_title"        => [
                "id"      => "step_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("step", StepsGUI::LANG_MODULE)
            ],
            "accepted"          => [
                "id"      => "accepted",
                "default" => true,
                "sort"    => false
            ],
            "user_lastname"     => [
                "id"      => "user_lastname",
                "default" => true,
                "sort"    => false
            ],
            "user_firstname"    => [
                "id"      => "user_firstname",
                "default" => true,
                "sort"    => false
            ],
            "user_email"        => [
                "id"      => "user_email",
                "default" => true,
                "sort"    => false
            ],
            "responsible_users" => [
                "id"      => "responsible_users",
                "default" => true,
                "sort"    => false
            ]
        ];

        foreach ($this->modifications as $modification) {
            $columns = array_merge($columns, $modification->getAdditionalColumns());
        }

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/*: void*/
    {
        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $filter = $this->getFilterValues();

        $responsible_users = $filter["responsible_users"];
        $object_title = $filter["object_title"];
        $workflow_id = $filter["workflow_id"];
        if (!empty($workflow_id)) {
            $workflow_id = intval($workflow_id);
        } else {
            $workflow_id = null;
        }
        $step_id = $filter["step_id"];
        if (!empty($step_id)) {
            $step_id = intval($step_id);
        } else {
            $step_id = null;
        }
        $accepted = $filter["accepted"];
        if (!empty($accepted)) {
            $accepted = ($accepted === "yes");
        } else {
            $accepted = null;
        }
        $user_lastname = $filter["user_lastname"];
        $user_firstname = $filter["user_firstname"];
        $user_email = $filter["user_email"];

        $data = self::srUserEnrolment()->enrolmentWorkflow()
            ->requests()
            ->getRequests($this->parent_obj->getObjRefId(), $step_id, null, $responsible_users, $object_title, $workflow_id, $accepted, $user_lastname, $user_firstname, $user_email);

        foreach ($this->modifications as $modification) {
            $modification->extendsAndFilterData($data, $filter);
        }

        $this->setData($data);
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [
            "object_title"      => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "workflow_id"       => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [0 => ""] + array_map(function (Workflow $workflow) : string {
                        return $workflow->getTitle();
                    }, self::srUserEnrolment()->enrolmentWorkflow()->workflows()->getWorkflows()),
                "setTitle"                        => self::plugin()->translate("workflow", WorkflowsGUI::LANG_MODULE)
            ],
            "step_id"           => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [0 => ""] + array_map(function (Step $step) : string {
                        return $step->getTitle();
                    }, self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps()),
                "setTitle"                        => self::plugin()->translate("step", StepsGUI::LANG_MODULE)
            ],
            "accepted"          => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    ""    => "",
                    "no"  => $this->txt("no"),
                    "yes" => $this->txt("yes")
                ],
                PropertyFormGUI::PROPERTY_VALUE   => "no"
            ],
            "user_lastname"     => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_firstname"    => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_email"        => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "responsible_users" => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => self::srUserEnrolment()->ruleEnrolment()->searchUsers(),
                "setAjaxLink"                     => self::dic()->ctrl()->getLinkTarget($this->parent_obj, RequestsGUI::CMD_GET_USERS_AUTO_COMPLETE, "", true, false)
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields());
        }
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("srusrenr_requests");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("requests"));
    }


    /**
     * @param Request $request
     */
    protected function fillRow(/*Request*/ $request)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(RequestInfoGUI::class, RequestInfoGUI::GET_PARAM_REQUEST_ID, $request->getRequestId());

        parent::fillRow($request);

        $actions = [];
        foreach (self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForAcceptRequest($request, self::dic()->user()->getId()) as $step) {
            self::dic()->ctrl()->setParameterByClass(AcceptRequestGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());

            $actions[] = self::dic()->ui()->factory()->link()->standard($step->getActionAcceptTitle(), self::dic()->ctrl()
                ->getLinkTargetByClass([RequestInfoGUI::class, AcceptRequestGUI::class], AcceptRequestGUI::CMD_ACCEPT_REQUEST));
        }
        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel($this->txt("actions"))));
    }
}
