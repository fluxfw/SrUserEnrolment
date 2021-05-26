<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilSrUserEnrolmentPlugin;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\EditRequestGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Fill\AbstractFillCtrl;

/**
 * Class FillCtrl
 *
 * @package           srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData\FillCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\EditRequestGUI
 */
class FillCtrl extends AbstractFillCtrl
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const RETURN_EDIT_STEP = 2;
    const RETURN_REQUEST_STEP = 1;
    /**
     * @var int
     */
    protected $return;


    /**
     * @inheritDoc
     *
     * @param int $return
     */
    public function __construct(int $parent_context, int $parent_id, int $return)
    {
        parent::__construct($parent_context, $parent_id);

        $this->return = $return;
    }


    /**
     * @inheritDoc
     */
    protected function back()/* : void*/
    {
        switch ($this->return) {
            case self::RETURN_REQUEST_STEP:
                self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP);
                break;

            case self::RETURN_EDIT_STEP:
                self::dic()->ctrl()->redirectByClass(EditRequestGUI::class, EditRequestGUI::CMD_EDIT_REQUEST);
                break;

            default:
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function cancel()/* : void*/
    {
        switch ($this->return) {
            case self::RETURN_REQUEST_STEP:
                self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_BACK);
                break;

            case self::RETURN_EDIT_STEP:
                self::dic()->ctrl()->redirectByClass(EditRequestGUI::class, EditRequestGUI::CMD_BACK);
                break;

            default:
                break;
        }
    }
}
