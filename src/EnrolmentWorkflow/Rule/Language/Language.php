<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language;

use srag\CustomInputGUIs\SrUserEnrolment\TabsInputGUI\MultilangualTabsInputGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class Language
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Language
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Language extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "lang";
    /**
     * @var string[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $languages = [];


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return nl2br(implode("\n", array_map(function (string $language) : string {
            return htmlspecialchars(strval(MultilangualTabsInputGUI::getLanguages()[$language]));
        }, $this->languages)), false);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "languages":
                return json_encode($field_value);

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "languages":
                return json_decode($field_value);

            default:
                return null;
        }
    }


    /**
     * @return string[]
     */
    public function getLanguages() : array
    {
        return $this->languages;
    }


    /**
     * @param string[] $languages
     */
    public function setLanguages(array $languages)/* : void*/
    {
        $this->languages = $languages;
    }
}
