<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request
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
     * @param Request $request
     */
    protected function deleteRequest(Request $request)/*: void*/
    {
        $request->delete();
    }


    /**
     * @param int $step_id
     */
    public function deleteRequests(int $step_id)/*: void*/
    {
        foreach ($this->getRequests(null, $step_id) as $request) {
            $this->deleteRequest($request);
        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Request::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_ref_id
     * @param int $step_id
     * @param int $user_id
     *
     * @return Request|null
     */
    public function getRequest(int $obj_ref_id, int $step_id, int $user_id)/*:?Request*/
    {
        /**
         * @var Request|null $request
         */

        $request = Request::where(["obj_id" => self::dic()->objDataCache()->lookupObjId($obj_ref_id), "step_id" => $step_id, "user_id" => $user_id])->first();

        return $request;
    }


    /**
     * @param int $request_id
     *
     * @return Request|null
     */
    public function getRequestById(int $request_id)/*: ?Request*/
    {
        /**
         * @var Request|null $request
         */

        $request = Request::where(["request_id" => $request_id])->first();

        return $request;
    }


    /**
     * @param int|null    $obj_ref_id
     * @param int|null    $step_id
     * @param int|null    $user_id
     * @param array|null  $responsible_user_ids
     * @param string|null $object_title
     * @param int|null    $workflow_id
     * @param bool|null   $accepted
     * @param string|null $user_lastname
     * @param string|null $user_firstname
     * @param string|null $user_email
     *
     * @return Request[]
     */
    public function getRequests(/*?*/ int $obj_ref_id = null, /*?*/ int $step_id = null,/*?*/ int $user_id = null,/*?*/ array $responsible_user_ids = null, /*?*/ string $object_title = null,/*?*/
        int $workflow_id = null, /*?*/ bool $accepted = null,/*?*/ string $user_lastname = null,/*?*/ string $user_firstname = null, /*?*/ string $user_email = null
    ) : array {
        if (!self::srUserEnrolment()->enrolmentWorkflow()->isEnabled()) {
            return []; // TODO:
        }

        $wheres = [];

        if (!empty($obj_ref_id)) {
            $wheres["obj_id"] = self::dic()->objDataCache()->lookupObjId($obj_ref_id);
        }

        if (!empty($step_id)) {
            $wheres["step_id"] = $step_id;
        }

        if (!empty($user_id)) {
            $wheres["user_id"] = $user_id;
        }

        if (!empty($responsible_user_ids)) {
            $wheres["responsible_users"] = $responsible_user_ids;
        }

        if ($accepted !== null) {
            $wheres["accepted"] = $accepted;
        }

        $requests = Request::where($wheres)->get();

        $requests = array_filter($requests, function (Request $request) : bool {
            return $this->hasRequestAccess($request, self::dic()->user()->getId());
        });

        if (!empty($object_title)) {
            $requests = array_filter($requests, function (Request $request) use ($object_title): bool {
                return (stripos($request->getObject()->getTitle(), $object_title) !== false);
            });
        }

        if (!empty($workflow_id)) {
            $requests = array_filter($requests, function (Request $request) use ($workflow_id): bool {
                return ($request->getStep()->getWorkflowId() === $workflow_id);
            });
        }

        if (!empty($user_lastname)) {
            $requests = array_filter($requests, function (Request $request) use ($user_lastname): bool {
                return (stripos($request->getUser()->getLastname(), $user_lastname) !== false);
            });
        }

        if (!empty($user_firstname)) {
            $requests = array_filter($requests, function (Request $request) use ($user_firstname): bool {
                return (stripos($request->getUser()->getFirstname(), $user_firstname) !== false);
            });
        }

        if (!empty($user_email)) {
            $requests = array_filter($requests, function (Request $request) use ($user_email): bool {
                return (stripos($request->getUser()->getEmail(), $user_email) !== false);
            });
        }

        return $requests;
    }


    /**
     * @param int|null $user_id
     * @param int|null $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(/*?*/ int $user_id = null, /*?*/ int $obj_ref_id = null) : bool
    {
        if (empty($user_id)) {
            // TODO: Remove if no CtrlMainMenu
            $user_id = self::dic()->user()->getId();
        }

        if (!self::srUserEnrolment()->enrolmentWorkflow()->isEnabled()) {
            return false;
        }

        if (empty($this->getRequests($obj_ref_id, null, null))) {
            return false;
        }

        if (self::srUserEnrolment()->userHasRole($user_id)) {
            return true;
        }

        if (!empty($obj_ref_id)) {
            if (self::dic()->access()->checkAccessOfUser($user_id, "manage_members", "", $obj_ref_id)) {
                return true;
            }
        }

        return (!empty($this->getRequests($obj_ref_id, null, null, [$user_id])));
    }


    /**
     * @param Request $request
     * @param int     $user_id
     *
     * @return bool
     */
    public function hasRequestAccess(Request $request, int $user_id) : bool
    {
        if ($request->getUserId() === $user_id) {
            return true;
        }

        if (self::srUserEnrolment()->userHasRole($user_id)) {
            return true;
        }

        if (self::dic()->access()->checkAccessOfUser($user_id, "manage_members", "", $request->getObjRefId())) {
            return true;
        }

        return false;
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Request::updateDB();
    }


    /**
     * @param int $obj_ref_id
     * @param int $step_id
     * @param int $user_id
     *
     * @return Request
     */
    public function request(int $obj_ref_id, int $step_id, int $user_id) : Request
    {
        $request = $this->getRequest($obj_ref_id, $step_id, $user_id);

        if ($request === null) {
            $request = $this->factory()->newInstance();

            $request->setUserId($user_id);
            $request->setObjRefId($obj_ref_id);
            $request->setObjId($request->getObject()->getId());
            $request->setStepId($step_id);

            $request_backup = clone $request;

            self::srUserEnrolment()->enrolmentWorkflow()->actions()->runActions($request);

            if ($request->getStepId() !== $request_backup->getStepId()) {

                if ($request->getStep()->getWorkflowId() === $request_backup->getStep()->getWorkflowId()) {
                    if (in_array($request->getStepId(), array_keys(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForAcceptRequest($request_backup, self::dic()->user()->getId())))) {
                        return $this->request($request->getObjRefId(), $request->getStepId(), $request->getUserId());
                    }

                    $request = $request_backup;
                } else {
                    if (in_array($request->getStepId(), array_keys(self::srUserEnrolment()->enrolmentWorkflow()->steps()->getStepsForAcceptRequest($request, self::dic()->user()->getId())))) {
                        return $this->request($request->getObjRefId(), $request->getStepId(), $request->getUserId());
                    }

                    $request = $request_backup;
                }
            }

            $this->storeRequest($request);

            self::srUserEnrolment()->requiredData()->fills()->storeFillValues($request->getRequestId());

            self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME, ilSrUserEnrolmentPlugin::EVENT_AFTER_REQUEST, [
                "request" => $request
            ]);
        }

        return $request;
    }


    /**
     * @param Request $request
     */
    public function storeRequest(Request $request)/*: void*/
    {
        if (empty($request->getRequestId())) {
            $request->setCreateTime(time());
        }

        $request->store();
    }
}
