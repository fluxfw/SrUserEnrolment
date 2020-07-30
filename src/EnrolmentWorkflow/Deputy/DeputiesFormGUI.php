<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ilCheckboxInputGUI;
use ilDateTimeInputGUI;
use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class DeputiesFormGUI
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeputiesFormGUI extends PropertyFormGUI
{

    use SrUserEnrolmentTrait;

    const LANG_MODULE = DeputiesGUI::LANG_MODULE;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var array
     */
    protected $deputies;


    /**
     * DeputiesFormGUI constructor
     *
     * @param DeputiesGUI $parent
     * @param array       $deputies
     */
    public function __construct(DeputiesGUI $parent, array $deputies)
    {
        $this->deputies = $deputies;

        parent::__construct($parent);
    }


    /**
     * @return array
     */
    public function getDeputies() : array
    {
        return $this->deputies;
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
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
        $this->addCommandButton(DeputiesGUI::CMD_UPDATE_DEPUTIES, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "deputies" => [
                self::PROPERTY_CLASS    => MultiLineNewInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    "deputy_user_id" => [
                        self::PROPERTY_CLASS      => MultiSelectSearchNewInputGUI::class,
                        self::PROPERTY_REQUIRED   => true,
                        "setTitle"                => $this->txt("user"),
                        "setAjaxAutoCompleteCtrl" => new UsersAjaxAutoCompleteCtrl(),
                        "setLimitCount"           => 1
                    ],
                    "until"          => [
                        self::PROPERTY_CLASS => ilDateTimeInputGUI::class
                    ],
                    "active"         => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ]
                ],
                "setShowSort"           => false
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
        $this->setTitle($this->txt(($this->parent->getUserId() === intval(self::dic()->user()->getId()) ? "my_" : "") . "deputies"));
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
}
