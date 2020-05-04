<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\TabsInputGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class StepFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class StepFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = StepsGUI::LANG_MODULE;
    /**
     * @var Step
     */
    protected $step;


    /**
     * StepFormGUI constructor
     *
     * @param StepGUI $parent
     * @param Step    $step
     */
    public function __construct(StepGUI $parent, Step $step)
    {
        $this->step = $step;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return Items::getter($this->step, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        if (!empty($this->step->getStepId())) {
            $this->addCommandButton(StepGUI::CMD_UPDATE_STEP, $this->txt("save"));
        } else {
            $this->addCommandButton(StepGUI::CMD_CREATE_STEP, $this->txt("add"));
            $this->addCommandButton(StepGUI::CMD_BACK, $this->txt("cancel"));
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "enabled"            => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "titles"             => [
                self::PROPERTY_CLASS    => TabsInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                    "title" => [
                        self::PROPERTY_CLASS => ilTextInputGUI::class
                    ]
                ], true),
                "setTitle"              => $this->txt("title")
            ],
            "action_titles"      => [
                self::PROPERTY_CLASS    => TabsInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                    "action_title" => [
                        self::PROPERTY_CLASS => ilTextInputGUI::class
                    ]
                ], true),
                "setTitle"              => $this->txt("action_title")
            ],
            "action_edit_titles" => [
                self::PROPERTY_CLASS    => TabsInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => MultilangualTabsInputGUI::generate([
                    "action_accept_title" => [
                        self::PROPERTY_CLASS => ilTextInputGUI::class
                    ]
                ], true),
                "setTitle"              => $this->txt("action_edit_title")
            ]
        ];
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
        $this->setTitle($this->txt(!empty($this->step->getStepId()) ? "edit_step" : "add_step"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->step, $key, $value);
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

        if (empty($this->step->getStepId())) {
            $this->step->setWorkflowId($this->parent->getParent()->getParent()->getWorkflow()->getWorkflowId());
        }

        self::srUserEnrolment()->enrolmentWorkflow()->steps()->storeStep($this->step);

        return true;
    }
}
