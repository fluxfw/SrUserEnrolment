<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Members;

use ilLink;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\ExcelImportGUI;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Rule\RulesCourseGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class MembersGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Members
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Members\MembersGUI: ilUIPluginRouterGUI
 */
class MembersGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_LIST_MEMBERS = "listMembers";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "members";
    const TAB_MEMBERS = "members";
    /**
     * @var int
     */
    protected $obj_ref_id;


    /**
     * MembersGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));

        if (!self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess($this->obj_ref_id, self::dic()->user()->getId())) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_LIST_MEMBERS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess($obj_ref_id, self::dic()->user()->getId())) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
            self::dic()
                ->tabs()
                ->addTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function redirect(int $obj_ref_id)/*: void*/
    {
        if (self::srUserEnrolment()->enrolmentWorkflow()->members()->hasAccess($obj_ref_id, self::dic()->user()->getId())) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->ctrl()->redirectByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], MembersGUI::CMD_LIST_MEMBERS);
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()
            ->tabs()
            ->addTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));

        RequestsGUI::addTabs($this->obj_ref_id);

        self::dic()
            ->tabs()
            ->addSubTab(self::TAB_MEMBERS, self::plugin()->translate("members", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_LIST_MEMBERS));

        RulesCourseGUI::addTabs($this->obj_ref_id);

        ExcelImportGUI::addTabs($this->obj_ref_id);
    }


    /**
     *
     */
    protected function back()/*:void*/
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->obj_ref_id));
    }


    /**
     *
     */
    protected function listMembers()/*:void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_MEMBERS);
        self::dic()->tabs()->activateSubTab(self::TAB_MEMBERS);

        $table = self::srUserEnrolment()->enrolmentWorkflow()->members()->factory()->newTableInstance($this);

        self::output()->output($table, true);
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }
}
