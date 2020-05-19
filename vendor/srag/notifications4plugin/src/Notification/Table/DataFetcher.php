<?php

namespace srag\Notifications4Plugin\SrUserEnrolment\Notification\Table;

use srag\DataTableUI\SrUserEnrolment\Component\Data\Data;
use srag\DataTableUI\SrUserEnrolment\Component\Data\Row\RowData;
use srag\DataTableUI\SrUserEnrolment\Component\Settings\Settings;
use srag\DataTableUI\SrUserEnrolment\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\Notifications4Plugin\SrUserEnrolment\Notification\NotificationInterface;
use srag\Notifications4Plugin\SrUserEnrolment\Utils\Notifications4PluginTrait;

/**
 * Class DataFetcher
 *
 * @package srag\Notifications4Plugin\SrUserEnrolment\Notification\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DataFetcher extends AbstractDataFetcher
{

    use Notifications4PluginTrait;

    /**
     * @inheritDoc
     */
    public function fetchData(Settings $settings) : Data
    {
        return self::dataTableUI()->data()->data(array_map(function (NotificationInterface $notification
        ) : RowData {
            return self::dataTableUI()->data()->row()->getter($notification->getId(), $notification);
        }, self::notifications4plugin()->notifications()->getNotifications($settings)),
            self::notifications4plugin()->notifications()->getNotificationsCount());
    }
}
