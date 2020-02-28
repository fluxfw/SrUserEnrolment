<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

use ArrayObject;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AssistantsRequestTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AssistantsRequestTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = AssistantsGUI::LANG_MODULE;
    /**
     * @var ArrayObject<AbstractAssistantsRequestTableModifications>
     */
    protected $modifications;


    /**
     * AssistantsRequestTableGUI constructor
     *
     * @param AssistantsRequestGUI $parent
     * @param string               $parent_cmd
     */
    public function __construct(AssistantsRequestGUI $parent, string $parent_cmd)
    {
        $this->modifications = new ArrayObject();

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_ASSISTANTS_REQUESTS_TABLE_MODIFICATIONS, [
            "modifications" => $this->modifications
        ]);

        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param Assistant $assistant
     */
    protected function getColumnValue(/*string*/ $column, /*Assistant*/ $assistant, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        foreach ($this->modifications as $modification) {
            $column_value = $modification->formatColumnValue($column, $assistant);
            if ($column_value !== null) {
                return $column_value;
            }
        }

        switch ($column) {
            case "user_lastname":
                $column = htmlspecialchars($assistant->getUser()->getLastname());
                break;

            case "user_firstname":
                $column = htmlspecialchars($assistant->getUser()->getFirstname());
                break;

            case "user_email":
                $column = htmlspecialchars($assistant->getUser()->getEmail());
                break;

            default:
                $column = htmlspecialchars(Items::getter($assistant, $column));
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
            "user_lastname"  => [
                "id"      => "user_lastname",
                "default" => true,
                "sort"    => false
            ],
            "user_firstname" => [
                "id"      => "user_firstname",
                "default" => true,
                "sort"    => false
            ],
            "user_email"     => [
                "id"      => "user_email",
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
        $this->addColumn("");

        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->setSelectAllCheckbox(RequestStepGUI::GET_PARAM_USER_ID);
        $this->addMultiCommand(RequestStepGUI::CMD_REQUEST_STEP, $this->parent_obj->getStep()->getActionTitle());
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $filter = $this->getFilterValues();

        $user_lastname = $filter["user_lastname"];
        $user_firstname = $filter["user_firstname"];
        $user_email = $filter["user_email"];

        $data = array_filter(self::srUserEnrolment()->enrolmentWorkflow()->assistants()->getAssistantsOf(self::dic()->user()->getId()),
            function (Assistant $assistant) use ($user_lastname, $user_firstname, $user_email) : bool {
                if (!in_array($this->parent_obj->getStep()->getStepId(),
                    array_keys(self::srUserEnrolment()
                        ->enrolmentWorkflow()
                        ->steps()
                        ->getStepsForRequest(AbstractRule::TYPE_STEP_ACTION, $assistant->getUserId(), $assistant->getUserId(), $this->parent_obj->getObjRefId())))
                ) {
                    return false;
                }

                if (!empty($user_lastname) && stripos($assistant->getUser()->getLastname(), $user_lastname) === false) {
                    return false;
                }

                if (!empty($user_firstname) && stripos($assistant->getUser()->getFirstname(), $user_firstname) === false) {
                    return false;
                }

                if (!empty($user_email) && stripos($assistant->getUser()->getEmail(), $user_email) === false) {
                    return false;
                }

                return true;
            });

        foreach ($this->modifications as $modification) {
            $data = $modification->extendsAndFilterData($data, $filter);
        }

        $this->setData($data);
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [
            "user_lastname"  => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_firstname" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "user_email"     => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
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
        $this->setId("srusrenr_assistants_requests");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("users"));
    }


    /**
     * @param Assistant $assistant
     */
    protected function fillRow(/*Assistant*/ $assistant)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, RequestsGUI::GET_PARAM_REF_ID, $this->parent_obj->getObjRefId());
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, StepGUI::GET_PARAM_STEP_ID, $this->parent_obj->getStep()->getStepId());
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, RequestStepGUI::GET_PARAM_USER_ID, $assistant->getUserId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariableEscaped("CHECKBOX_POST_VAR", RequestStepGUI::GET_PARAM_USER_ID);
        $this->tpl->setVariableEscaped("ID", $assistant->getUserId());
        $this->tpl->parseCurrentBlock();

        parent::fillRow($assistant);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->link()->standard($this->parent_obj->getStep()->getActionTitle(), self::dic()->ctrl()
                ->getLinkTargetByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP))
        ])->withLabel($this->txt("actions"))));
    }
}
