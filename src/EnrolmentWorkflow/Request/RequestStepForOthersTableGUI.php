<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ArrayObject;
use ilObjUser;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestStepForOthersTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 */
class RequestStepForOthersTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = AssistantsGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var ArrayObject<AbstractRequestStepForOthersTableModifications>
     */
    protected $modifications;


    /**
     * RequestStepForOthersTableGUI constructor
     *
     * @param RequestStepForOthersGUI $parent
     * @param string                  $parent_cmd
     */
    public function __construct(RequestStepForOthersGUI $parent, string $parent_cmd)
    {
        $this->modifications = new ArrayObject();

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUEST_STEP_FOR_OTHERS_TABLE_MODIFICATIONS, [
            "modifications" => $this->modifications
        ]);

        parent::__construct($parent, $parent_cmd);
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
     * @param ilObjUser $user
     */
    protected function fillRow(/*ilObjUser*/ $user)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, RequestsGUI::GET_PARAM_REF_ID, $this->parent_obj->getObjRefId());
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, StepGUI::GET_PARAM_STEP_ID, $this->parent_obj->getStep()->getStepId());
        self::dic()->ctrl()->setParameterByClass(RequestStepGUI::class, RequestStepGUI::GET_PARAM_USER_ID, $user->getId());

        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariableEscaped("CHECKBOX_POST_VAR", RequestStepGUI::GET_PARAM_USER_ID);
        $this->tpl->setVariableEscaped("ID", $user->getId());
        $this->tpl->parseCurrentBlock();

        parent::fillRow($user);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->link()->standard($this->parent_obj->getStep()->getActionTitle(), self::dic()->ctrl()
                ->getLinkTargetByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP))
        ])->withLabel($this->txt("actions"))));
    }


    /**
     * @inheritDoc
     *
     * @param ilObjUser $user
     */
    protected function getColumnValue(string $column, /*ilObjUser*/ $user, int $format = self::DEFAULT_FORMAT) : string
    {
        foreach ($this->modifications as $modification) {
            $column_value = $modification->formatColumnValue($column, $user);
            if ($column_value !== null) {
                return $column_value;
            }
        }

        switch ($column) {
            case "user_lastname":
                $column = htmlspecialchars($user->getLastname());
                break;

            case "user_firstname":
                $column = htmlspecialchars($user->getFirstname());
                break;

            case "user_email":
                $column = htmlspecialchars($user->getEmail());
                break;

            default:
                $column = htmlspecialchars(Items::getter($user, $column));
                break;
        }

        return strval($column);
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

        $data = array_filter(self::srUserEnrolment()->enrolmentWorkflow()->requests()->getPossibleUsersForRequestStepForOthers(self::dic()->user()->getId()),
            function (ilObjUser $user) use ($user_lastname, $user_firstname, $user_email) : bool {
                if (!in_array($this->parent_obj->getStep()->getStepId(),
                    array_keys(self::srUserEnrolment()
                        ->enrolmentWorkflow()
                        ->steps()
                        ->getStepsForRequest(AbstractRule::TYPE_STEP_ACTION, $user->getId(), $user->getId(), $this->parent_obj->getObjRefId())))
                ) {
                    return false;
                }

                if (!empty($user_lastname) && stripos($user->getLastname(), $user_lastname) === false) {
                    return false;
                }

                if (!empty($user_firstname) && stripos($user->getFirstname(), $user_firstname) === false) {
                    return false;
                }

                if (!empty($user_email) && stripos($user->getEmail(), $user_email) === false) {
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
        $this->setId(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_request_for_others");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("users"));
    }
}
