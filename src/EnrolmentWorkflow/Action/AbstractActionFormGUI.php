<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilCheckboxInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class AbstractActionFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractActionFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = ActionsGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var AbstractAction
     */
    protected $action;


    /**
     * AbstractActionFormGUI constructor
     *
     * @param ActionGUI      $parent
     * @param AbstractAction $action
     */
    public function __construct(ActionGUI $parent, AbstractAction $action)
    {
        $this->action = $action;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        self::srUserEnrolment()->enrolmentWorkflow()->actions()->storeAction($this->action);

        return true;
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return Items::getter($this->action, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ActionGUI::CMD_UPDATE_ACTION, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*:void*/
    {
        $this->fields = [
            "enabled"          => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "run_next_actions" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
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
        $this->setTitle($this->txt("edit_action"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->action, $key, $value);
                break;
        }
    }
}
