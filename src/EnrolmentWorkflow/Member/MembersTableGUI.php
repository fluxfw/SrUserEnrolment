<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ArrayObject;
use ilLearningProgressBaseGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\CheckboxInputGUI\AjaxCheckbox;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\TableGUI\TableGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepsGUI;
use srag\Plugins\SrUserEnrolment\ResetPassword\ResetPasswordGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class MembersTableGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MembersTableGUI extends TableGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = MembersGUI::LANG_MODULE;
    /**
     * @var ArrayObject<AbstractMembersTableModifications>
     */
    protected $modifications;


    /**
     * MembersTableGUI constructor
     *
     * @param MembersGUI $parent
     * @param string     $parent_cmd
     */
    public function __construct(MembersGUI $parent, string $parent_cmd)
    {
        $this->modifications = new ArrayObject();

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_MEMBERS_TABLE_MODIFICATIONS, [
            "modifications" => $this->modifications
        ]);

        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param Member $member
     */
    protected function getColumnValue(/*string*/ $column, /*Member*/ $member, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        foreach ($this->modifications as $modification) {
            $column_value = $modification->formatColumnValue($column, $member);
            if ($column_value !== null) {
                return $column_value;
            }
        }

        switch ($column) {
            case "member_type":
                $column = htmlspecialchars($this->txt("member_type_" . Member::TYPES[$member->getType()]));
                break;

            case "user_firstname":
                $column = htmlspecialchars($member->getUser()->getFirstname());
                break;

            case "user_lastname":
                $column = htmlspecialchars($member->getUser()->getLastname());
                break;

            case "user_email":
                $column = htmlspecialchars($member->getUser()->getEmail());
                break;

            case "user_department":
                $column = htmlspecialchars($member->getUser()->getDepartment());
                break;

            case "member_learning_progress":
                if ($member->getLpStatus() !== null) {
                    self::dic()->language()->loadLanguageModule("trac");

                    $column = self::output()->getHTML([
                        self::dic()
                            ->ui()
                            ->factory()
                            ->image()
                            ->standard(ilLearningProgressBaseGUI::_getImagePathForStatus($member->getLpStatus()), ilLearningProgressBaseGUI::_getStatusText($member->getLpStatus())),
                        htmlspecialchars(ilLearningProgressBaseGUI::_getStatusText($member->getLpStatus()))
                    ]);
                } else {
                    $column = "";
                }
                break;

            case "member_completed":
                if ($member->isLpCompleted() !== null) {
                    $column = self::output()->getHTML((new AjaxCheckbox())->withChecked($member->isLpCompleted())->withAjaxChangeLink(self::dic()
                        ->ctrl()
                        ->getLinkTargetByClass(MemberGUI::class, MemberGUI::CMD_SET_COMPLETED, "", true, false)));
                } else {
                    $column = "";
                }
                break;

            case "request_step_title":
                if ($member->getRequest() !== null) {
                    $column = htmlspecialchars($member->getRequest()->getStep()->getTitle());
                } else {
                    $column = "";
                }
                break;

            default:
                $column = htmlspecialchars(Items::getter($member, $column));
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
            "member_type"              => [
                "id"      => "member_type",
                "default" => true,
                "sort"    => false
            ],
            "user_firstname"           => [
                "id"      => "user_firstname",
                "default" => true,
                "sort"    => false
            ],
            "user_lastname"            => [
                "id"      => "user_lastname",
                "default" => true,
                "sort"    => false
            ],
            "user_email"               => [
                "id"      => "user_email",
                "default" => true,
                "sort"    => false
            ],
            "user_department"          => [
                "id"      => "user_department",
                "default" => true,
                "sort"    => false
            ],
            "member_learning_progress" => [
                "id"      => "member_learning_progress",
                "default" => true,
                "sort"    => false
            ],
            "member_completed"         => [
                "id"      => "member_completed",
                "default" => true,
                "sort"    => false
            ],
            "request_step_title"       => [
                "id"      => "request_step_title",
                "default" => true,
                "sort"    => false,
                "txt"     => self::plugin()->translate("step", StepsGUI::LANG_MODULE)
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

        $data = self::srUserEnrolment()->enrolmentWorkflow()->members()->getMembers($this->parent_obj->getObjRefId());

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
        $this->filter_fields = [];

        foreach ($this->modifications as $modification) {
            $this->filter_fields = array_merge($this->filter_fields, $modification->getAdditionalFilterFields());
        }
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_members");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("members"));
    }


    /**
     * @param Member $member
     */
    protected function fillRow(/*Member*/ $member)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(MemberGUI::class, MemberGUI::GET_PARAM_USER_ID, $member->getUsrId());

        parent::fillRow($member);

        $actions = [];

        $reset_password_action = ResetPasswordGUI::getAction($member->getObjRefId(), $member->getUsrId());
        if ($reset_password_action !== null) {
            $actions[] = $reset_password_action;
        }

        if ($member->getType() !== Member::TYPE_REQUEST) {
            $actions[] = self::dic()->ui()->factory()->link()->standard($this->txt("edit_member"), self::dic()->ctrl()
                ->getLinkTargetByClass(MemberGUI::class, MemberGUI::CMD_EDIT_MEMBER));

            $actions[] = self::dic()->ui()->factory()->link()->standard($this->txt("remove_member"), self::dic()->ctrl()
                ->getLinkTargetByClass(MemberGUI::class, MemberGUI::CMD_REMOVE_MEMBER_CONFIRM));

            $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel($this->txt("actions"))));
        }

        self::dic()->ctrl()->setParameterByClass(MemberGUI::class, MemberGUI::GET_PARAM_USER_ID, null);
    }
}
