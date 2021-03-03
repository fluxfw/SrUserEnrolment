<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class UsersMembersAjaxAutoCompleteCtrl
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\UsersMembersAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member\MembersGUI
 */
class UsersMembersAjaxAutoCompleteCtrl extends UsersAjaxAutoCompleteCtrl
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var MembersGUI
     */
    protected $parent;


    /**
     * UsersMembersAjaxAutoCompleteCtrl constructor
     *
     * @param MembersGUI $parent
     */
    public function __construct(MembersGUI $parent)
    {
        parent::__construct();

        $this->parent = $parent;
    }


    /**
     * @inheritDoc
     */
    protected function formatUsers(array $users) : array
    {
        $members = self::srUserEnrolment()->enrolmentWorkflow()->members()->getMembers($this->parent->getObjRefId());

        return parent::formatUsers(array_filter($users, function (array $user) use ($members) : bool {
            return (!isset($members[$user["usr_id"]]) || $members[$user["usr_id"]]->getType() === Member::TYPE_REQUEST);
        }));
    }
}
