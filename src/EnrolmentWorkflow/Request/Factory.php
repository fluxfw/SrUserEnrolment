<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilSrUserEnrolmentPlugin;
use srag\CustomInputGUIs\SrUserEnrolment\PropertyFormGUI\Items\Items;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
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
     * Factory constructor
     */
    private function __construct()
    {

    }


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
     * @return RequestGroup
     */
    public function newGroupInstance() : RequestGroup
    {
        $request_group = new RequestGroup();

        return $request_group;
    }


    /**
     * @return Request
     */
    public function newInstance() : Request
    {
        $request = new Request();

        return $request;
    }


    /**
     * @param RequestStepForOthersGUI $parent
     * @param string                  $cmd
     *
     * @return RequestStepForOthersTableGUI
     */
    public function newRequestStepForOthersTableInstance(RequestStepForOthersGUI $parent, string $cmd = RequestStepForOthersGUI::CMD_LIST_USERS) : RequestStepForOthersTableGUI
    {
        $table = new RequestStepForOthersTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param RequestsGUI $parent
     * @param string      $cmd
     *
     * @return AbstractRequestsTableGUI
     */
    public function newTableInstance(RequestsGUI $parent, string $cmd = RequestsGUI::CMD_LIST_REQUESTS) : AbstractRequestsTableGUI
    {
        $class = str_replace("Abstract", ucfirst(Items::strToCamelCase(RequestsGUI::getRequestsTypes()[$parent->getRequestsType()])), AbstractRequestsTableGUI::class);

        $table = new $class($parent, $cmd);

        return $table;
    }
}
