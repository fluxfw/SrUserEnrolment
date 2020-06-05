<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Group;

use srag\RequiredData\SrUserEnrolment\Field\FieldsCtrl;

/**
 * Class GroupsCtrl
 *
 * @package           srag\RequiredData\SrUserEnrolment\Field\Field\Group
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\RequiredData\SrUserEnrolment\Field\Field\Group\GroupsCtrl: srag\RequiredData\SrUserEnrolment\Field\FieldCtrl
 */
class GroupsCtrl extends FieldsCtrl
{

    /**
     * @inheritDoc
     */
    protected function setTabs()/* : void*/
    {
        parent::setTabs();

        self::addTabs();
    }


    /**
     * @inheritDoc
     */
    public function getFieldCtrlClass() : string
    {
        return GroupCtrl::class;
    }


    /**
     * @inheritDoc
     */
    protected function createGroupOfFields()/* : void*/
    {
        die();
    }
}
