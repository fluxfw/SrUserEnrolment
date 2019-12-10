<?php

namespace srag\RequiredData\SrUserEnrolment\Field;

use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class CreateFieldFormGUI
 *
 * @package srag\RequiredData\SrUserEnrolment\Field
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CreateFieldFormGUI extends ObjectPropertyFormGUI
{

    use RequiredDataTrait;
    const LANG_MODULE = FieldsCtrl::LANG_MODULE;
    /**
     * @var AbstractField
     */
    protected $object;
    /**
     * @var string
     */
    protected $type;


    /**
     * CreateFieldFormGUI constructor
     *
     * @param FieldCtrl $parent
     */
    public function __construct(FieldCtrl $parent)
    {
        $this->type = current(array_keys(self::requiredData()->fields()->factory()->getClasses()));

        parent::__construct($parent, null, false);
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
        $this->addCommandButton(FieldCtrl::CMD_CREATE_FIELD, $this->txt("add"));
        $this->addCommandButton(FieldCtrl::CMD_BACK, $this->txt("cancel"));
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
                        "setTitle"           => self::requiredData()->fields()->factory()->newInstance($class::getType())->getTypeTitle()
                    ];
                }, self::requiredData()->fields()->factory()->getClasses())
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
        $this->setTitle($this->txt("add_field"));
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

        $this->object = self::requiredData()->fields()->factory()->newInstance($this->type);

        $this->object->setParentContext($this->parent->getParent()->getParentContext());
        $this->object->setParentId($this->parent->getParent()->getParentId());

        self::requiredData()->fields()->storeField($this->object);

        return true;
    }


    /**
     * @inheritDoc
     */
    public function txt(/*string*/ $key,/*?string*/ $default = null) : string
    {
        if ($default !== null) {
            return self::requiredData()->getPlugin()->translate($key, self::LANG_MODULE, [], true, "", $default);
        } else {
            return self::requiredData()->getPlugin()->translate($key, self::LANG_MODULE);
        }
    }
}
