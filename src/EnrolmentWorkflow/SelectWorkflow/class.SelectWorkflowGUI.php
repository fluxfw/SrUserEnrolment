<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow;

use ilLink;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class SelectWorkflowGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow\SelectWorkflowGUI: ilUIPluginRouterGUI
 */
class SelectWorkflowGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_BACK = "back";
    const CMD_SELECT_WORKFLOW = "selectWorkflow";
    const CMD_UPDATE_SELECTED_WORKFLOW = "updateSelectedWorkflow";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "select_workflow";
    const TAB_SELECT_WORKFLOW = "select_workflow";
    /**
     * @var int
     */
    protected $obj_ref_id;


    /**
     * SelectWorkflowGUI constructor
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

        if (!self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
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
                    case self::CMD_SELECT_WORKFLOW:
                    case self::CMD_UPDATE_SELECTED_WORKFLOW:
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
        if (self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()->tabs()->addTab(self::TAB_SELECT_WORKFLOW, self::plugin()->translate("select_workflow", self::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_SELECT_WORKFLOW));
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->setBackTarget(self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($this->obj_ref_id)), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()->tabs()->addTab(self::TAB_SELECT_WORKFLOW, self::plugin()->translate("select_workflow", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_SELECT_WORKFLOW));
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->obj_ref_id));
    }


    /**
     *
     */
    protected function selectWorkflow()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SELECT_WORKFLOW);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->factory()->newFormInstance($this);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateSelectedWorkflow()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SELECT_WORKFLOW);

        $form = self::srUserEnrolment()->enrolmentWorkflow()->selectedWorkflows()->factory()->newFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SELECT_WORKFLOW);
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }
}
