<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI\UsersAjaxAutoCompleteCtrl;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class UsersAssistantsAjaxAutoCompleteCtrl
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\UsersAssistantsAjaxAutoCompleteCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI
 */
class UsersAssistantsAjaxAutoCompleteCtrl extends UsersAjaxAutoCompleteCtrl
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var RequestsGUI
     */
    protected $parent;


    /**
     * UsersAssistantsAjaxAutoCompleteCtrl constructor
     *
     * @param RequestsGUI $parent
     */
    public function __construct(RequestsGUI $parent)
    {
        parent::__construct();

        $this->parent = $parent;
    }


    /**
     * @inheritDoc
     */
    protected function formatUsers(array $users) : array
    {
        return parent::formatUsers(array_filter($users, function (array $user) : bool {
            return self::srUserEnrolment()
                ->enrolmentWorkflow()
                ->requests()
                ->canRequestWithAssistant($this->parent->getObjRefId(), current(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getSteps(self::srUserEnrolment()
                    ->enrolmentWorkflow()
                    ->selectedWorkflows()
                    ->getWorkflowId(self::dic()->objDataCache()->lookupObjId($this->parent->getObjRefId()))))->getStepId(), self::dic()->user()->getId(), $user["usr_id"]);
        }));
    }
}
