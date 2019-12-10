<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData;

use ilSrUserEnrolmentPlugin;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\AcceptRequestGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Fill\AbstractFillCtrl;

/**
 * Class FillCtrl
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FillCtrl extends AbstractFillCtrl
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var bool
     */
    protected $accept;


    /**
     * @inheritDoc
     *
     * @param bool $accept
     */
    public function __construct(int $parent_context, int $parent_id, bool $accept = false)
    {
        parent::__construct($parent_context, $parent_id);

        $this->accept = $accept;
    }


    /**
     * @inheritDoc
     */
    protected function back()/* : void*/
    {
        if ($this->accept) {
            self::dic()->ctrl()->redirectByClass(AcceptRequestGUI::class, AcceptRequestGUI::CMD_ACCEPT_REQUEST);
        } else {
            self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP);
        }
    }


    /**
     * @inheritDoc
     */
    protected function cancel()/* : void*/
    {
        if ($this->accept) {
            self::dic()->ctrl()->redirectByClass(AcceptRequestGUI::class, AcceptRequestGUI::CMD_BACK);
        } else {
            self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_BACK);
        }
    }
}
