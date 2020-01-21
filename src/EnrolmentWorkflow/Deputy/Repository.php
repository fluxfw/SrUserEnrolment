<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy;

use ActiveRecordList;
use ilDate;
use ilDBConstants;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Config\ConfigFormGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy
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
     * @param ActiveRecordList $where
     *
     * @return ActiveRecordList
     */
    protected function activeCheck(ActiveRecordList $where) : ActiveRecordList
    {
        return $where->where("(until IS NULL OR until>=" . self::dic()->database()->quote(time(), ilDBConstants::T_INTEGER) . ")")->where([
            "active" => true
        ]);
    }


    /**
     * @param Deputy $deputy
     */
    public function deleteDeputy(Deputy $deputy)/*:void*/
    {
        $deputy->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Deputy::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int  $user_id
     * @param int  $deputy_user_id
     * @param bool $deputy_user_id
     *
     * @return Deputy|null
     */
    public function getDeputy(int $user_id, int $deputy_user_id, bool $active_check = true)/* : ?Deputy*/
    {
        $where = Deputy::where([
            "user_id"        => $user_id,
            "deputy_user_id" => $deputy_user_id
        ]);

        if ($active_check) {
            $where = $this->activeCheck($where);
        }

        /**
         * @var Deputy|null $deputy
         */
        $deputy = $where->first();

        return $deputy;
    }


    /**
     * @return Deputy[]
     */
    public function getDeputies() : array
    {
        return Deputy::get();
    }


    /**
     * @param int  $deputy_user_id
     * @param bool $active_check
     *
     * @return Deputy[]
     */
    public function getDeputiesOf(int $deputy_user_id, bool $active_check = true) : array
    {
        $where = Deputy::where([
            "deputy_user_id" => $deputy_user_id
        ]);

        if ($active_check) {
            $where = $this->activeCheck($where);
        }

        return $where->get();
    }


    /**
     * @param int  $user_id
     * @param bool $active_check
     *
     * @return Deputy[]
     */
    public function getUserDeputies(int $user_id, bool $active_check = true) : array
    {
        $where = Deputy::where([
            "user_id" => $user_id
        ]);

        if ($active_check) {
            $where = $this->activeCheck($where);
        }

        return $where->get();
    }


    /**
     * @param int $user_id
     *
     * @return array
     */
    public function getUserDeputiesArray(int $user_id) : array
    {
        return array_map(function (Deputy $deputy) : array {
            return [
                "deputy_user_id" => $deputy->getDeputyUserId(),
                "until"          => $deputy->getUntil(),
                "active"         => $deputy->isActive()
            ];
        }, $this->getUserDeputies($user_id, false));
    }


    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id) : bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if ($user_id === ANONYMOUS_USER_ID) {
            return false;
        }

        if ($user_id === intval(self::dic()->user()->getId())) {
            return true;
        }

        return self::dic()->access()->checkAccessOfUser(self::dic()->user()->getId(), "write", "", 7);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Deputy::updateDB();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::srUserEnrolment()->enrolmentWorkflow()->isEnabled() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_DEPUTIES));
    }


    /**
     * @param int      $user_id
     * @param Deputy[] $deputies
     *
     * @return Deputy[]
     */
    protected function storeUserDeputies(int $user_id, array $deputies) : array
    {
        foreach ($this->getUserDeputies($user_id, false) as $deputy) {
            $this->deleteDeputy($deputy);
        }

        foreach ($deputies as $deputy) {
            $this->storeDeputy($deputy);
        }

        return $deputies;
    }


    /**
     * @param int   $user_id
     * @param array $deputies
     *
     * @return Deputy[]
     */
    public function storeUserDeputiesArray(int $user_id, array $deputies) : array
    {
        return $this->storeUserDeputies($user_id, array_map(function (array $array) use ($user_id): Deputy {
                $deputy = $this->factory()->newInstance();

                $deputy->setUserId($user_id);

                $deputy->setDeputyUserId($array["deputy_user_id"]);

                $deputy->setUntil($array["until"] ? new ilDate($array["until"], IL_CAL_DATE) : null);

                $deputy->setActive(boolval($array["active"]));

                return $deputy;
            }, $deputies)
        );
    }


    /**
     * @param Deputy $deputy
     */
    public function storeDeputy(Deputy $deputy)/*:void*/
    {
        $deputy->store();
    }
}
