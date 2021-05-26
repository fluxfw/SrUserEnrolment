<?php

namespace srag\RequiredData\SrUserEnrolment\Field\Field\Group;

require_once __DIR__ . "/../../../../../../autoload.php";

use srag\RequiredData\SrUserEnrolment\Field\FieldCtrl;

/**
 * Class GroupCtrl
 *
 * @package           srag\RequiredData\SrUserEnrolment\Field\Field\Group
 *
 * @ilCtrl_isCalledBy srag\RequiredData\SrUserEnrolment\Field\Field\Group\GroupCtrl: srag\RequiredData\SrUserEnrolment\Field\Field\Group\GroupsCtrl
 */
class GroupCtrl extends FieldCtrl
{

    /**
     * @inheritDoc
     */
    protected function ungroup()/* : void*/
    {
        die();
    }
}
