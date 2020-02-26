<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilCheckboxInputGUI;
use ilDate;
use ilDatePresentation;
use ilNonEditableValueGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class MemberFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MemberFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = MembersGUI::LANG_MODULE;
    /**
     * @var Member
     */
    protected $member;
    /**
     * @var AbstractMemberFormModifications[]
     */
    protected $modifications = [];


    /**
     * MemberFormGUI constructor
     *
     * @param MemberGUI $parent
     * @param Member    $member
     */
    public function __construct(MemberGUI $parent, Member $member)
    {
        $this->member = $member;

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_COLLECT_MEMBER_FORM_MODIFICATIONS, [
            "modifications" => &$this->modifications
        ]);

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "completed":
                return $this->member->isLpCompleted();

            case "updated_time":
                return ilDatePresentation::formatDate(new ilDate($this->member->getUpdatedTime(), IL_CAL_UNIX));

            case "updated_user":
                return $this->member->getUpdatedUser()->getFullname();

            default:
                foreach ($this->modifications as $modification) {
                    $value = $modification->getValue($this->member, $key);
                    if ($value !== null) {
                        return $value;
                    }
                }

                return Items::getter($this->member, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(MemberGUI::CMD_UPDATE_MEMBER, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "updated_time" => [
                self::PROPERTY_CLASS => ilNonEditableValueGUI::class
            ],
            "updated_user" => [
                self::PROPERTY_CLASS => ilNonEditableValueGUI::class
            ],
            "completed"    => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => $this->txt("member_completed")
            ]
        ];

        foreach ($this->modifications as $modification) {
            $this->fields = array_merge($this->fields, $modification->getAdditionalFields());
        }
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("edit_member"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "completed":
                $this->member->setLpCompleted($value);
                break;

            case "updated_time":
            case "updated_user":
                break;

            default:
                foreach ($this->modifications as $modification) {
                    if ($modification->storeValue($this->member, $key, $value)) {
                        return;
                    }
                }

                Items::setter($this->member, $key, $value);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        self::srUserEnrolment()->enrolmentWorkflow()->members()->storeMember($this->member);

        return true;
    }
}
