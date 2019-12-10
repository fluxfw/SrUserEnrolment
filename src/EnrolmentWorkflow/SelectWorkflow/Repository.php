<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow;

use ilObjCourse;
use ilObjectFactory;
use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\SelectWorkflow
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param SelectedWorkflow $selected_workflow
     */
    protected function deleteSelectedWorkflow(SelectedWorkflow $selected_workflow)/*: void*/
    {
        $selected_workflow->delete();
    }


    /**
     * @param int $workflow_id
     */
    public function deleteSelectedWorkflows(int $workflow_id)/*: void*/
    {
        foreach ($this->getSelectedWorkflows($workflow_id) as $selected_workflow) {
            $this->deleteSelectedWorkflow($selected_workflow);
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(SelectedWorkflow::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_id
     *
     * @return SelectedWorkflow|null
     */
    protected function getSelectedWorkflow(int $obj_id)/* : ?SelectedWorkflow*/
    {
        /**
         * @var SelectedWorkflow|null $selected_workflow
         */

        $selected_workflow = SelectedWorkflow::where(["obj_id" => $obj_id])->first();

        return $selected_workflow;
    }


    /**
     * @param int $workflow_id
     *
     * @return SelectedWorkflow[]
     */
    protected function getSelectedWorkflows(int $workflow_id) : array
    {
        return SelectedWorkflow::where(["workflow_id" => $workflow_id])->get();
    }


    /**
     * @param int $obj_id
     *
     * @return int|null
     */
    public function getWorkflowId(int $obj_id)/* : ?int*/
    {
        $selected_workflow = $this->getSelectedWorkflow($obj_id);

        if ($selected_workflow !== null) {
            return $selected_workflow->getWorkflowId();
        }

        return null;
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id) : bool
    {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->isEnabled()) {
            return false;
        }

        if (!self::srUserEnrolment()->userHasRole($user_id)) {
            return false;
        }

        return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        SelectedWorkflow::updateDB();
    }


    /**
     * @param int      $obj_id
     * @param int|null $workflow_id
     *
     * @return int|null
     */
    public function setWorkflowId(int $obj_id,/*?*/ int $workflow_id = null)//*:void*/
    {
        $selected_workflow = $this->getSelectedWorkflow($obj_id);

        if ($selected_workflow !== null) {
            if (!empty($workflow_id)) {
                $selected_workflow->setWorkflowId($workflow_id);
                $this->storeSelectedWorkflow($selected_workflow);
            } else {
                $this->deleteSelectedWorkflow($selected_workflow);
                $selected_workflow = null;
            }
        } else {
            if (!empty($workflow_id)) {
                $selected_workflow = $this->factory()->newInstance();
                $selected_workflow->setObjId($obj_id);
                $selected_workflow->setWorkflowId($workflow_id);
                $this->storeSelectedWorkflow($selected_workflow);
            }
        }

        if ($selected_workflow !== null) {
            $obj = ilObjectFactory::getInstanceByObjId($selected_workflow->getObjId(), false);
            if ($obj instanceof ilObjCourse) {
                if (intval($obj->getSubscriptionLimitationType()) !== IL_CRS_SUBSCRIPTION_DEACTIVATED) {
                    // Modules/Course/classes/class.ilObjCourseGUI.php:912
                    $obj->setSubscriptionType(IL_CRS_SUBSCRIPTION_DIRECT);
                    $obj->setSubscriptionLimitationType(IL_CRS_SUBSCRIPTION_DEACTIVATED);

                    $obj->updateSettings();

                    self::dic()->language()->loadLanguageModule("crs");

                    ilUtil::sendInfo(self::plugin()->translate("object_setting_changed", SelectWorkflowGUI::LANG_MODULE, [
                        self::dic()->language()->txt("crs_registration_type"),
                        self::dic()->language()->txt("crs_reg_no_selfreg")
                    ]), true);
                }
            }
        }

        return null;
    }


    /**
     * @param SelectedWorkflow $selected_workflow
     */
    protected function storeSelectedWorkflow(SelectedWorkflow $selected_workflow)/*: void*/
    {
        $selected_workflow->store();
    }
}
