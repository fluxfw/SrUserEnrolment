<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ArrayObject;
use ilDatePresentation;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractRequestsTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractRequestsTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = RequestsGUI::LANG_MODULE;
    /**
     * @var ArrayObject<AbstractRequestsTableModifications>
     */
    protected $modifications;


    /**
     * AbstractRequestsTableGUI constructor
     *
     * @param RequestsGUI $parent
     * @param string      $parent_cmd
     */
    public function __construct(RequestsGUI $parent, string $parent_cmd)
    {
        $this->modifications = new ArrayObject();

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS, [
            "modifications" => $this->modifications
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
            case "created_time":
                $column = htmlspecialchars($request->getFormattedCreatedTime());
                break;

            case "created_time_group":
                $column = htmlspecialchars($request->getRequestGroup()->getFormattedCreatedTime());
                break;

            case "created_user":
                $column = htmlspecialchars($request->getCreatedUser()->getFullname());
                break;

            case "edited":
                if ($request->isEdited()) {
                    $column = ilUtil::getImagePath("icon_ok.svg");
                } else {
                    $column = ilUtil::getImagePath("icon_not_ok.svg");
                }
                $column = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column, ""));
                break;

            case "edited_status":
                $column = self::output()->getHTML([
                    self::dic()
                        ->ui()
                        ->factory()
                        ->image()
                        ->standard(self::plugin()->directory() . "/templates/images/edited_status/" . RequestGroup::EDITED_STATUS[$request->getRequestGroup()->getEditedStatus()] . ".svg",
                            $this->txt("edited_status_" . RequestGroup::EDITED_STATUS[$request->getRequestGroup()->getEditedStatus()])),
                    htmlspecialchars($this->txt("edited_status_" . RequestGroup::EDITED_STATUS[$request->getRequestGroup()->getEditedStatus()]))
                ]);
                break;

            case "edited_time":
                $column = htmlspecialchars($request->getFormattedEditedTime());
                break;

            case "edited_user":
                $column = htmlspecialchars($request->getEditedUser()->getFullname());
                break;

            case "object_title":
                $column = htmlspecialchars($request->getObject()->getTitle());
                $column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, $request->getRequestLink(!empty($this->parent_obj->getObjRefId()))));
                break;

            case "object_start":
                if ($request->getObject()->getCourseStart()) {
                    $column = htmlspecialchars(ilDatePresentation::formatDate($request->getObject()->getCourseStart()));
                } else {
                    $column = "";
                }
                break;

            case "object_end":
                if ($request->getObject()->getCourseEnd()) {
                    $column = htmlspecialchars(ilDatePresentation::formatDate($request->getObject()->getCourseEnd()));
                } else {
                    $column = "";
                }
                break;

            case "workflow_title":
                $column = htmlspecialchars($request->getWorkflow()->getTitle());
                break;

            case "step_title":
                $column = htmlspecialchars($request->getStep()->getTitle());
                break;

            case "user_firstname":
                $column = htmlspecialchars($request->getUser()->getFirstname());
                break;

            case "user_lastname":
                $column = htmlspecialchars($request->getUser()->getLastname());
                break;

            case "user_email":
                $column = htmlspecialchars($request->getUser()->getEmail());
                break;

            case "user_org_units":
                $column = htmlspecialchars($request->getUser()->getOrgUnitsRepresentation());
                break;

            case "responsible_users":
                $column = nl2br(implode("\n", array_map(function (ilObjUser $responsible_user) : string {
                    return htmlspecialchars($responsible_user->getFullname());
                }, $request->getFormattedResponsibleUsers())), false);
                break;

            default:
                $column = htmlspecialchars(Items::getter($request, $column));
                break;
        }

        return strval($column);
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

        $data = self::srUserEnrolment()->enrolmentWorkflow()
            ->requests()
            ->getRequests($this->getFilterObjRefId(), $this->getFilterStepId(), $this->getFilterUserId(), $this->getFilterResponsibleUsers(),
                $this->getFilterObjectTitle(),
                $this->getFilterWorkflowId(), $this->getFilterEdited(), $this->getFilterEditedStatus(), $this->getFilterUserLastname(), $this->getFilterUserFirstname(), $this->getFilterUserEmail(),
                $this->getFilterUserOrgUnits());

        foreach ($this->modifications as $modification) {
            $data = $modification->extendsAndFilterData($data, $filter);
        }

        $this->setData($data);
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("srusrenr_requests_" . $this->parent_obj->getRequestsType());
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("type_" . RequestsGUI::getRequestsTypes()[$this->parent_obj->getRequestsType()]));
    }


    /**
     * @param Request $request
     */
    protected function fillRow(/*Request*/ $request)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(RequestInfoGUI::class, RequestInfoGUI::GET_PARAM_REQUEST_ID, $request->getRequestId());

        parent::fillRow($request);

        $actions = [];
        foreach (self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($request, self::dic()->user()->getId()) as $step) {
            self::dic()->ctrl()->setParameterByClass(EditRequestGUI::class, StepGUI::GET_PARAM_STEP_ID, $step->getStepId());

            $actions[] = self::dic()->ui()->factory()->link()->standard($step->getActionEditTitle(), self::dic()->ctrl()
                ->getLinkTargetByClass([RequestInfoGUI::class, EditRequestGUI::class], EditRequestGUI::CMD_EDIT_REQUEST));
        }
        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel($this->txt("actions"))));
    }


    /**
     * @return bool|null
     */
    protected abstract function getFilterEdited()/* : ?bool*/ ;


    /**
     * @return int[]|null
     */
    protected abstract function getFilterEditedStatus()/* : ?array*/ ;


    /**
     * @return int|null
     */
    protected abstract function getFilterObjRefId()/* : ?int*/ ;


    /**
     * @return string|null
     */
    protected abstract function getFilterObjectTitle()/* : ?string*/ ;


    /**
     * @return array|null
     */
    protected abstract function getFilterResponsibleUsers()/* : ?array*/ ;


    /**
     * @return int|null
     */
    protected abstract function getFilterStepId()/* : ?int*/ ;


    /**
     * @return array|null
     */
    protected abstract function getFilterUserId()/* : ?array*/ ;


    /**
     * @return string|null
     */
    protected abstract function getFilterUserEmail()/* : ?string*/ ;


    /**
     * @return string|null
     */
    protected abstract function getFilterUserFirstname()/* : ?string*/ ;


    /**
     * @return string|null
     */
    protected abstract function getFilterUserLastname()/* : ?string*/ ;


    /**
     * @return string|null
     */
    protected abstract function getFilterUserOrgUnits()/* : ?string*/ ;


    /**
     * @return int|null
     */
    protected abstract function getFilterWorkflowId()/* : ?int*/ ;
}
