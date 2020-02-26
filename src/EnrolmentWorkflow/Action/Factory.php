<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AssignResponsibleUsers\AssignResponsibleUsers;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\CreateCourse\CreateCourse;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\EnrollToCourse\EnrollToCourse;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToStep\MoveToStep;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\MoveToWorkflow\MoveToWorkflow;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification\SendNotification;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
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
     * @var array
     */
    protected $classes
        = [
            AssignResponsibleUsers::class,
            CreateCourse::class,
            EnrollToCourse::class,
            MoveToStep::class,
            MoveToWorkflow::class,
            SendNotification::class
        ];


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @param string $class
     */
    public function addClass(string $class)/*:void*/
    {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }
    }


    /**
     * @return string[]
     */
    public function getTypes() : array
    {
        $types = array_combine(array_map(function (string $class) : string {
            return $class::getType();
        }, $this->classes), $this->classes);

        ksort($types);

        return $types;
    }


    /**
     * @param string $type
     *
     * @return AbstractAction|null
     */
    public function newInstance(string $type) /*: ?AbstractAction*/
    {
        $action = null;

        foreach ($this->getTypes() as $type_class => $class) {
            if ($type_class === $type) {
                $action = new $class();
                break;
            }
        }

        return $action;
    }


    /**
     * @param ActionsGUI $parent
     * @param string     $cmd
     *
     * @return ActionsTableGUI
     */
    public function newTableInstance(ActionsGUI $parent, string $cmd = ActionsGUI::CMD_LIST_ACTIONS) : ActionsTableGUI
    {
        $table = new ActionsTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param ActionGUI $parent
     *
     * @return CreateActionFormGUI
     */
    public function newCreateFormInstance(ActionGUI $parent) : CreateActionFormGUI
    {
        $form = new CreateActionFormGUI($parent);

        return $form;
    }


    /**
     * @param ActionGUI      $parent
     * @param AbstractAction $action
     *
     * @return AbstractActionFormGUI
     */
    public function newFormInstance(ActionGUI $parent, AbstractAction $action) : AbstractActionFormGUI
    {
        $class = get_class($action) . "FormGUI";

        $form = new $class($parent, $action);

        return $form;
    }


    /**
     * @param AbstractAction $action
     *
     * @return AbstractActionRunner
     */
    public function newRunnerInstance(AbstractAction $action) : AbstractActionRunner
    {
        $class = get_class($action) . "Runner";

        $runner = new $class($action);

        return $runner;
    }
}
