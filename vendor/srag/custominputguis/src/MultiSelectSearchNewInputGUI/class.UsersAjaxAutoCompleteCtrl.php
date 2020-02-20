<?php

namespace srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI;

use ilDBConstants;
use ilObjUser;

/**
 * Class UsersAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UsersAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * UsersAjaxAutoCompleteCtrl constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(string $search = null) : array
    {
        return $this->formatUsers(ilObjUser::searchUsers($search));
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        return $this->formatUsers(self::dic()->database()->fetchAll(self::dic()->database()->query('SELECT usr_id, firstname, lastname, login FROM usr_data WHERE ' . self::dic()
                ->database()
                ->in("usr_id", $ids, false, ilDBConstants::T_INTEGER))));
    }


    /**
     * @param array $users
     *
     * @return array
     */
    protected function formatUsers(array $users) : array
    {
        $formatted_users = [];

        foreach ($users as $user) {
            $formatted_users[$user["usr_id"]] = $user["firstname"] . " " . $user["lastname"] . " (" . $user["login"] . ")";
        }

        return $formatted_users;
    }
}
