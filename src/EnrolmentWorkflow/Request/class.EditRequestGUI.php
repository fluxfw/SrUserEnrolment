<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilSrUserEnrolmentPlugin;
use ilUtil;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\Step;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Step\StepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class EditRequestGUI
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\EditRequestGUI: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\EditRequestGUI
 */
class EditRequestGUI
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const CMD_EDIT_REQUEST = "editRequest";
    const CMD_BACK = "back";
    /**
     * @var RequestInfoGUI
     */
    protected $parent;
    /**
     * @var Step
     */
    protected $step;


    /**
     * EditRequestGUI constructor
     *
     * @param RequestInfoGUI $parent
     */
    public function __construct(RequestInfoGUI $parent)
    {
        $this->parent = $parent;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->step = self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepById(intval(filter_input(INPUT_GET, StepGUI::GET_PARAM_STEP_ID)));

        if (
            self::dic()->ctrl()->getCmd() !== self::CMD_BACK
            && !in_array($this->step->getStepId(),
                array_keys(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForEditRequest($this->parent->getRequest(), self::dic()->user()->getId())))
        ) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, StepGUI::GET_PARAM_STEP_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(FillCtrl::class):
                self::dic()->ctrl()->forwardCommand(new FillCtrl(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId(), FillCtrl::RETURN_EDIT_STEP));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_EDIT_REQUEST:
                    case self::CMD_BACK:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::srUserEnrolment()->requiredData()->fills()->clearTempFillValues();

        self::dic()->ctrl()->redirectByClass(RequestsGUI::class, RequestsGUI::CMD_LIST_REQUESTS);
    }


    /**
     *
     */
    protected function editRequest()/*: void*/
    {
        $required_data_fields = self::srUserEnrolment()->requiredData()->fields()->getFields(Step::REQUIRED_DATA_PARENT_CONTEXT_STEP, $this->step->getStepId());

        if (!empty($required_data_fields)) {
            $required_data = self::srUserEnrolment()->requiredData()->fills()->getFillValues();

            if (empty($required_data)) {
                self::dic()->ctrl()->redirectByClass([FillCtrl::class], FillCtrl::CMD_FILL_FIELDS);

                return;
            }
        } else {
            $required_data = null;
        }

        $this->parent->getRequest()->setEdited(true);
        $this->parent->getRequest()->setEditedTime(time());
        $this->parent->getRequest()->setEditedUserId(self::dic()->user()->getId());
        self::srUserEnrolment()->enrolmentWorkflow()->requests()->storeRequest($this->parent->getRequest());

        self::srUserEnrolment()->enrolmentWorkflow()->requests()->request($this->parent->getRequest()->getObjRefId(), $this->step->getStepId(), $this->parent->getRequest()->getUserId(),
            $required_data);

        ilUtil::sendSuccess(self::plugin()
            ->translate("edited_message", RequestsGUI::LANG_MODULE, [$this->parent->getRequest()->getStep()->getTitle()]),
            true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }
}
