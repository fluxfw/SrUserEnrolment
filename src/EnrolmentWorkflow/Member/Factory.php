<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Member
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
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
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Member
     */
    public function newInstance() : Member
    {
        $member = new Member();

        return $member;
    }


    /**
     * @param MembersGUI $parent
     * @param string     $cmd
     *
     * @return MembersTableGUI
     */
    public function newTableInstance(MembersGUI $parent, string $cmd = MembersGUI::CMD_LIST_MEMBERS) : MembersTableGUI
    {
        $table = new MembersTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param MemberGUI $parent
     * @param Member    $member
     *
     * @return MemberFormGUI
     */
    public function newFormInstance(MemberGUI $parent, Member $member) : MemberFormGUI
    {
        $form = new MemberFormGUI($parent, $member);

        return $form;
    }
}
