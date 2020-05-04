<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class CreateActionFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateActionFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const LANG_MODULE = ActionsGUI::LANG_MODULE;
    /**
     * @var AbstractAction|null
     */
    protected $action = null;
    /**
     * @var string
     */
    protected $type;


    /**
     * CreateActionFormGUI constructor
     *
     * @param ActionGUI $parent
     */
    public function __construct(ActionGUI $parent)
    {
        $this->type = current(array_keys(self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->getTypes()));

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return $this->{$key};
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ActionGUI::CMD_CREATE_ACTION, $this->txt("add"));
        $this->addCommandButton(ActionGUI::CMD_BACK, $this->txt("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "type" => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                self::PROPERTY_SUBITEMS => array_map(function (string $class) : array {
                    return [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newInstance($class::getType())->getTypeTitle()
                    ];
                }, self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->getTypes())
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
        $this->setTitle($this->txt("add_action"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                $this->{$key} = $value;
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

        $this->action = self::srUserEnrolment()->enrolmentWorkflow()->actions()->factory()->newInstance($this->type);

        $this->action->setStepId($this->parent->getParent()->getParent()->getStep()->getStepId());

        self::srUserEnrolment()->enrolmentWorkflow()->actions()->storeAction($this->action);

        return true;
    }


    /**
     * @return AbstractAction
     */
    public function getAction() : AbstractAction
    {
        return $this->action;
    }
}
