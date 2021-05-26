<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractAction;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestGroup;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;

/**
 * Class SetEditedStatus
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SetEditedStatus
 */
class SetEditedStatus extends AbstractAction
{

    const TABLE_NAME_SUFFIX = "ses";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $edited_status = RequestGroup::EDITED_STATUS_NOT_EDITED;


    /**
     * @inheritDoc
     */
    public function getActionDescription() : string
    {
        return self::plugin()->translate("edited_status_" . RequestGroup::EDITED_STATUS[$this->edited_status], RequestsGUI::LANG_MODULE);
    }


    /**
     * @return int
     */
    public function getEditedStatus() : int
    {
        return $this->edited_status;
    }


    /**
     * @param int $edited_status
     */
    public function setEditedStatus(int $edited_status)/* : void*/
    {
        $this->edited_status = $edited_status;
    }
}
