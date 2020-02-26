<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant;

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
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant
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
     * @param Assistant $assistant
     */
    public function deleteAssistant(Assistant $assistant)/*:void*/
    {
        $assistant->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Assistant::TABLE_NAME, false);
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
     * @param int  $assistant_user_id
     * @param bool $active_check
     * @param bool $create_new
     *
     * @return Assistant|null
     */
    public function getAssistant(int $user_id, int $assistant_user_id, bool $active_check = true, bool $create_new = false)/* : ?Assistant*/
    {
        $where = Assistant::where([
            "user_id"           => $user_id,
            "assistant_user_id" => $assistant_user_id
        ]);

        if ($active_check) {
            $where = $this->activeCheck($where);
        }

        /**
         * @var Assistant|null $assistant
         */
        $assistant = $where->first();

        if ($assistant === null && $create_new) {
            $assistant = $this->factory()->newInstance();

            $assistant->setUserId($user_id);

            $assistant->setAssistantUserId($assistant_user_id);

            $this->storeAssistant($assistant);
        }

        return $assistant;
    }


    /**
     * @return Assistant[]
     */
    public function getAssistants() : array
    {
        return Assistant::get();
    }


    /**
     * @param int  $assistant_user_id
     * @param bool $active_check
     *
     * @return Assistant[]
     */
    public function getAssistantsOf(int $assistant_user_id, bool $active_check = true) : array
    {
        $where = Assistant::where([
            "assistant_user_id" => $assistant_user_id
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
     * @return Assistant[]
     */
    public function getUserAssistants(int $user_id, bool $active_check = true) : array
    {
        $where = Assistant::where([
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
    public function getUserAssistantsArray(int $user_id) : array
    {
        return array_map(function (Assistant $assistant) : array {
            return [
                "assistant_user_id" => [$assistant->getAssistantUserId()],
                "until"             => $assistant->getUntil(),
                "active"            => $assistant->isActive()
            ];
        }, $this->getUserAssistants($user_id, false));
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
        Assistant::updateDB();
    }


    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (self::srUserEnrolment()->enrolmentWorkflow()->isEnabled() && self::srUserEnrolment()->config()->getValue(ConfigFormGUI::KEY_SHOW_ASSISTANTS));
    }


    /**
     * @param int         $user_id
     * @param Assistant[] $assistants
     *
     * @return Assistant[]
     */
    protected function storeUserAssistants(int $user_id, array $assistants) : array
    {
        foreach ($this->getUserAssistants($user_id, false) as $assistant) {
            $this->deleteAssistant($assistant);
        }

        foreach ($assistants as $assistant) {
            $this->storeAssistant($assistant);
        }

        return $assistants;
    }


    /**
     * @param int   $user_id
     * @param array $assistants
     *
     * @return Assistant[]
     */
    public function storeUserAssistantsArray(int $user_id, array $assistants) : array
    {
        return $this->storeUserAssistants($user_id, array_map(function (array $array) use ($user_id): Assistant {
                $assistant = $this->factory()->newInstance();

                $assistant->setUserId($user_id);

                $assistant->setAssistantUserId(current($array["assistant_user_id"]));

                $assistant->setUntil($array["until"] ? new ilDate($array["until"], IL_CAL_DATE) : null);

                $assistant->setActive(boolval($array["active"]));

                return $assistant;
            }, $assistants)
        );
    }


    /**
     * @param Assistant $assistant
     */
    public function storeAssistant(Assistant $assistant)/*:void*/
    {
        $assistant->store();
    }
}
