<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
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
     * @param AbstractAction $action
     */
    public function deleteAction(AbstractAction $action)/*: void*/
    {
        $action->delete();

        $this->reSortActions($action->getStepId());

        self::srUserEnrolment()->enrolmentWorkflow()->rules()->deleteRules(AbstractRule::PARENT_CONTEXT_ACTION, $action->getId());
    }


    /**
     * @param int $step_id
     */
    public function deleteActions(int $step_id)/*: void*/
    {
        foreach ($this->getActions($step_id, false) as $action) {
            $this->deleteAction($action);
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        foreach ($this->factory()->getTypes() as $class) {
            self::dic()->database()->dropTable($class::getTableName(), false);
        }
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param string $type
     * @param int    $action_id
     *
     * @return AbstractAction|null
     */
    public function getActionById(string $type, int $action_id)/*: ?Action*/
    {
        foreach ($this->factory()->getTypes() as $type_class => $class) {
            if ($type_class === $type) {
                /**
                 * @var AbstractAction|null $action
                 */
                $action = $class::where(["action_id" => $action_id])->first();

                return $action;
            }
        }

        return null;
    }


    /**
     * @param int  $step_id
     * @param bool $only_enabled
     *
     * @return AbstractAction[]
     */
    public function getActions(int $step_id, bool $only_enabled = true) : array
    {
        $actions = [];

        foreach ($this->factory()->getTypes() as $class) {
            $where = $class::where(["step_id" => $step_id]);

            if ($only_enabled) {
                $where = $where->where(["enabled" => true]);
            }

            /**
             * @var AbstractAction $action
             */
            foreach ($where->get() as $action) {
                $actions[$action->getId()] = $action;
            }
        }

        uasort($actions, function (AbstractAction $action1, AbstractAction $action2) : int {
            if ($action1->getSort() < $action2->getSort()) {
                return -1;
            }
            if ($action1->getSort() > $action2->getSort()) {
                return 1;
            }

            return 0;
        });

        return $actions;
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        foreach ($this->factory()->getTypes() as $class) {
            $upgrade_init_run_next_actions = (self::dic()->database()->tableExists($class::getTableName()) && !self::dic()->database()->tableColumnExists($class::getTableName(), "run_next_actions"));

            $class::updateDB();

            if ($upgrade_init_run_next_actions) {
                foreach ($class::get() as $action) {
                    /**
                     * @var AbstractAction $action
                     */
                    $action->setRunNextActions($action->getInitRunNextActions());

                    $this->storeAction($action);
                }
            }
        }
    }


    /**
     * @param AbstractAction $action
     */
    public function moveActionDown(AbstractAction $action)/*: void*/
    {
        $action->setSort($action->getSort() + 15);

        $this->storeAction($action);

        $this->reSortActions($action->getStepId());
    }


    /**
     * @param AbstractAction $action
     */
    public function moveActionUp(AbstractAction $action)/*: void*/
    {
        $action->setSort($action->getSort() - 15);

        $this->storeAction($action);

        $this->reSortActions($action->getStepId());
    }


    /**
     * @param Request $request
     */
    public function runActions(Request $request)/*:void*/
    {
        foreach ($this->getActions($request->getStepId()) as $action) {
            if (empty(self::srUserEnrolment()->enrolmentWorkflow()
                ->rules()
                ->getCheckedRules(AbstractRule::PARENT_CONTEXT_ACTION, $action->getId(), AbstractRule::TYPE_ACTION_IF, $request->getUserId(), $request->getObjRefId(), false, $request))
            ) {
                continue;
            }

            $this->factory()->newRunnerInstance($action)->run($request);

            if (!$action->isRunNextActions()) {
                break;
            }
        }
    }


    /**
     * @param AbstractAction $action
     */
    public function storeAction(AbstractAction $action)/*: void*/
    {
        if (empty($action->getActionId())) {
            $action->setSort(((count($this->getActions($action->getStepId(), false)) + 1) * 10));
        }

        $action->store();
    }


    /**
     * @param int $step_id
     */
    protected function reSortActions(int $step_id)/*: void*/
    {
        $actions = $this->getActions($step_id, false);

        $i = 1;
        foreach ($actions as $action) {
            $action->setSort($i * 10);

            $this->storeAction($action);

            $i++;
        }
    }
}
