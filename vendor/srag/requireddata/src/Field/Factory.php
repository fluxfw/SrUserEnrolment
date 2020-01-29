<?php

namespace srag\RequiredData\SrUserEnrolment\Field;

use srag\DIC\SrUserEnrolment\DICTrait;
use srag\RequiredData\SrUserEnrolment\Field\Checkbox\CheckboxField;
use srag\RequiredData\SrUserEnrolment\Field\Date\DateField;
use srag\RequiredData\SrUserEnrolment\Field\Email\EmailField;
use srag\RequiredData\SrUserEnrolment\Field\Float\FloatField;
use srag\RequiredData\SrUserEnrolment\Field\Integer\IntegerField;
use srag\RequiredData\SrUserEnrolment\Field\MultilineText\MultilineTextField;
use srag\RequiredData\SrUserEnrolment\Field\MultiSearchSelect\MultiSearchSelectField;
use srag\RequiredData\SrUserEnrolment\Field\MultiSelect\MultiSelectField;
use srag\RequiredData\SrUserEnrolment\Field\Radio\RadioField;
use srag\RequiredData\SrUserEnrolment\Field\SearchSelect\SearchSelectField;
use srag\RequiredData\SrUserEnrolment\Field\Select\SelectField;
use srag\RequiredData\SrUserEnrolment\Field\Text\TextField;
use srag\RequiredData\SrUserEnrolment\Utils\RequiredDataTrait;

/**
 * Class Factory
 *
 * @package srag\RequiredData\SrUserEnrolment\Field
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use RequiredDataTrait;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var array
     */
    protected $classes
        = [
            CheckboxField::class,
            DateField::class,
            EmailField::class,
            FloatField::class,
            IntegerField::class,
            MultilineTextField::class,
            MultiSearchSelectField::class,
            MultiSelectField::class,
            RadioField::class,
            SelectField::class,
            SearchSelectField::class,
            TextField::class
        ];


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @param string $class
     */
    public function addClass(string $class)/*:void*/
    {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }
    }


    /**
     * @param bool     $check_can_be_added_only_once
     * @param int|null $parent_context
     * @param int|null $parent_id
     *
     * @return string[]
     */
    public function getClasses(bool $check_can_be_added_only_once = false,/*?*/ int $parent_context = null, /*?*/ int $parent_id = null) : array
    {
        $classes = array_combine(array_map(function (string $class) : string {
            return $class::getType();
        }, $this->classes), $this->classes);

        if ($check_can_be_added_only_once) {
            $classes = array_filter($classes, function (string $class) use ($parent_context, $parent_id): bool {
                if ($class::canBeAddedOnlyOnce()) {
                    return empty(self::requiredData()->fields()->getFields($parent_context, $parent_id, [
                        $class::getType()
                    ], false));
                } else {
                    return true;
                }
            });
        }

        return $classes;
    }


    /**
     * @param string $type
     *
     * @return AbstractField|null
     */
    public function newInstance(string $type) /*: ?AbstractField*/
    {
        $field = null;

        foreach ($this->getClasses() as $type_class => $class) {
            if ($type_class === $type) {
                $field = new $class();
                break;
            }
        }

        return $field;
    }


    /**
     * @param FieldsCtrl $parent
     * @param string     $cmd
     *
     * @return FieldsTableGUI
     */
    public function newTableInstance(FieldsCtrl $parent, string $cmd = FieldsCtrl::CMD_LIST_FIELDS) : FieldsTableGUI
    {
        $table = new FieldsTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param FieldCtrl $parent
     *
     * @return CreateFieldFormGUI
     */
    public function newCreateFormInstance(FieldCtrl $parent) : CreateFieldFormGUI
    {
        $form = new CreateFieldFormGUI($parent);

        return $form;
    }


    /**
     * @param FieldCtrl     $parent
     * @param AbstractField $field
     *
     * @return AbstractFieldFormGUI
     */
    public function newFormInstance(FieldCtrl $parent, AbstractField $field) : AbstractFieldFormGUI
    {
        $class = get_class($field) . "FormGUI";

        $form = new $class($parent, $field);

        return $form;
    }
}
