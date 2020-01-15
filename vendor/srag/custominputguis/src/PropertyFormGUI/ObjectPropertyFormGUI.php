<?php

namespace srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI;

use ActiveRecord;
use ilObject;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;

/**
 * Class ObjectPropertyFormGUI
 *
 * @package srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ObjectPropertyFormGUI extends PropertyFormGUI
{

    /**
     * @var ilObject|ActiveRecord|object|null
     */
    protected $object;
    /**
     * @var bool
     */
    protected $object_auto_store;


    /**
     * ObjectPropertyFormGUI constructor
     *
     * @param object                            $parent
     * @param ilObject|ActiveRecord|object|null $object
     * @param bool                              $object_auto_store
     */
    public function __construct($parent, $object = null,/*bool*/ $object_auto_store = true)
    {
        $this->object = $object;
        $this->object_auto_store = $object_auto_store;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        if ($this->object !== null) {
            switch ($key) {
                default:
                    return Items::getter($this->object, $key);
                    break;
            }
        }

        return null;
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->object, $key, $value);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function storeForm()/*: bool*/
    {
        if ($this->object === null) {
            // TODO:
            //$this->object = new Object();
        }

        if (!parent::storeForm()) {
            return false;
        }

        if ($this->object_auto_store) {
            if (method_exists($this->object, "store")) {
                $this->object->store();
            } else {
                if ($this->object instanceof ilObject) {
                    if ($this->object->getId()) {
                        $this->object->update();
                    } else {
                        $this->object->create();
                    }
                } else {
                    if (method_exists($this->object, "save")) {
                        $this->object->save();
                    } else {
                        if (method_exists($this->object, "update")) {
                            $this->object->update();
                        }
                    }
                }
            }
        }

        return true;
    }


    /**
     * @return ilObject|ActiveRecord|object
     */
    public final function getObject()
    {
        return $this->object;
    }
}
