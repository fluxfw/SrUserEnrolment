<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\AbstractActionRunner;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\Deputy;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Request;

/**
 * Class SendNotificationRunner
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Action\SendNotification
 */
class SendNotificationRunner extends AbstractActionRunner
{

    /**
     * @var SendNotification
     */
    protected $action;


    /**
     * @inheritDoc
     */
    public function __construct(SendNotification $action)
    {
        parent::__construct($action);
    }


    /**
     * @inheritDoc
     */
    public function run(Request $request)/*:void*/
    {
        $notification = self::srUserEnrolment()->notifications4plugin()->notifications()->getNotificationByName($this->action->getNotificationName());

        $placeholders = [
            "request" => $request
        ];

        switch ($this->action->getToType()) {
            case SendNotification::TO_TYPE_REQUESTOR:
                $users = [$request->getUserId()];
                break;

            case SendNotification::TO_TYPE_RESPONSIBLE_USERS:
                $users = $request->getResponsibleUsers();
                break;

            case SendNotification::TO_TYPE_SPECIFIC_USERS:
                $users = $this->action->getToSpecificUsers();
                break;

            case SendNotification::TO_TYPE_RESPONSIBLE_USERS_AND_DEPUTIES:
                $users = array_unique(array_reduce($request->getResponsibleUsers(), function (array $users, int $user_id) : array {
                    $users[] = $user_id;

                    $users = array_merge($users, array_map(function (Deputy $deputy) : int {
                        return $deputy->getDeputyUserId();
                    }, self::srUserEnrolment()->enrolmentWorkflow()->deputies()->getUserDeputies($user_id)));

                    return $users;
                }, []));
                break;

            default:
                $users = [];
                break;
        }

        foreach ($users as $user_id) {
            $sender = self::srUserEnrolment()->notifications4plugin()->sender()->factory()->internalMail(ANONYMOUS_USER_ID, $user_id);

            self::srUserEnrolment()->notifications4plugin()->sender()->send($sender, $notification, $placeholders, $request->getUser()->getLanguage());
        }
    }
}
