<?php

namespace srag\Plugins\SrUserEnrolment\Comment;

use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use srag\CommentsUI\SrUserEnrolment\Ctrl\AbstractCtrl;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class RequestCommentsCtrl
 *
 * @package           srag\Plugins\SrUserEnrolment\Comment
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrUserEnrolment\Comment\RequestCommentsCtrl: srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestInfoGUI
 */
class RequestCommentsCtrl extends AbstractCtrl
{

    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var RequestInfoGUI
     */
    protected $parent;


    /**
     * RequestCommentsCtrl constructor
     *
     * @param RequestInfoGUI $parent
     */
    public function __construct(RequestInfoGUI $parent)
    {
        parent::__construct();

        $this->parent = $parent;
    }


    /**
     * @inheritDoc
     */
    public function getAsyncClass() : array
    {
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_OBJ_ID, $this->parent->getRequest()->getRequestGroup()->getRequestGroupId());

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_USER_ID, $this->parent->getRequest()->getUserId());

        if ($this->parent->isSingle()) {
            return [
                ilUIPluginRouterGUI::class,
                RequestInfoGUI::class,
                self::class
            ];
        } else {
            return [
                ilUIPluginRouterGUI::class,
                RequestsGUI::class,
                RequestInfoGUI::class,
                self::class
            ];
        }
    }


    /**
     * @inheritDoc
     */
    public function getCommentsArray(int $report_obj_id, int $report_user_id) : array
    {
        if ($this->parent->isSingle()) {
            return self::srUserEnrolment()->comments()->getCommentsForCurrentUser($report_obj_id);
        } else {
            return self::srUserEnrolment()->comments()->getCommentsForReport($report_obj_id, $report_user_id);
        }
    }


    /**
     * @inheritDoc
     */
    public function getIsReadOnly() : bool
    {
        return $this->parent->isSingle();
    }


    /**
     * @inheritDoc
     */
    protected function createComment()/*: void*/
    {
        if (!$this->parent->isSingle()) {
            parent::createComment();
        }
    }


    /**
     * @inheritDoc
     */
    protected function updateComment()/*: void*/
    {
        if (!$this->parent->isSingle()) {
            parent::updateComment();
        }
    }


    /**
     * @inheritDoc
     */
    protected function deleteComment()/*: void*/
    {
        if (!$this->parent->isSingle()) {
            parent::deleteComment();
        }
    }


    /**
     * @inheritDoc
     */
    protected function shareComment()/*: void*/
    {
        if (!$this->parent->isSingle()) {
            parent::shareComment();
        }
    }
}
