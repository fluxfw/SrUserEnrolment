<?php

namespace srag\Plugins\SrUserEnrolment\Logs;

use ilDateTime;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Log\Log;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Logs
 *
 * @package srag\Plugins\SrUserEnrolment\Logs
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Logs {

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
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var Log[][]
	 */
	protected $kept_logs = [];


	/**
	 * Logs constructor
	 */
	private function __construct() {

	}


	/**
	 * @param int             $object_id
	 * @param array           $fields
	 * @param string|null     $sort_by
	 * @param string|null     $sort_by_direction
	 * @param int|null        $limit_start
	 * @param int|null        $limit_end
	 * @param string|null     $message
	 * @param ilDateTime|null $date_start
	 * @param ilDateTime|null $date_end
	 * @param int|null        $status
	 *
	 * @return array
	 */
	public function getLogs(int $object_id, array $fields = [], string $sort_by = null, string $sort_by_direction = null, int $limit_start = null, int $limit_end = null, string $message = null, ilDateTime $date_start = null, ilDateTime $date_end = null, int $status = null): array {

		if (!in_array("log_id", $fields)) {
			array_unshift($fields, "log_id");
		}

		$sql = 'SELECT ' . implode(",", array_map(function (string $field): string {
				return self::dic()->database()->quoteIdentifier($field);
			}, $fields));

		$sql .= $this->getLogsQuery($object_id, $sort_by, $sort_by_direction, $limit_start, $limit_end, $message, $date_start, $date_end, $status);

		$result = self::dic()->database()->query($sql);

		$logs = [];

		while (($row = $result->fetchAssoc()) !== false) {
			$logs[$row["log_id"]] = $row;
		}

		return $logs;
	}


	/**
	 * @param int             $object_id
	 * @param string|null     $message
	 * @param ilDateTime|null $date_start
	 * @param ilDateTime|null $date_end
	 * @param int|null        $status
	 *
	 * @return int
	 */
	public function getLogsCount(int $object_id, string $message = null, ilDateTime $date_start = null, ilDateTime $date_end = null, int $status = null): int {

		$sql = 'SELECT COUNT(log_id) AS count';

		$sql .= $this->getLogsQuery($object_id, null, null, null, null, $message, $date_start, $date_end, $status);

		$result = self::dic()->database()->query($sql);

		if (($row = $result->fetchAssoc()) !== false) {
			return intval($row["count"]);
		}

		return 0;
	}


	/**
	 * @param int             $object_id
	 * @param string|null     $sort_by
	 * @param string|null     $sort_by_direction
	 * @param int|null        $limit_start
	 * @param int|null        $limit_end
	 * @param string|null     $message
	 * @param ilDateTime|null $date_start
	 * @param ilDateTime|null $date_end
	 * @param int|null        $status
	 *
	 * @return string
	 */
	private function getLogsQuery(int $object_id, string $sort_by = null, string $sort_by_direction = null, int $limit_start = null, int $limit_end = null, string $message = null, ilDateTime $date_start = null, ilDateTime $date_end = null, int $status = null): string {

		$sql = ' FROM ' . Log::TABLE_NAME;

		$wheres = [
			'object_id=' . self::dic()->database()->quote($object_id, "integer")
		];

		if (!empty($message)) {
			$wheres[] = self::dic()->database()->like("message", "text", '%' . $message . '%');
		}

		if (!empty($date_start)) {
			$wheres[] = 'date>=' . self::dic()->database()->quote($date_start->get(IL_CAL_DATETIME), "text");
		}

		if (!empty($date_end)) {
			$wheres[] = 'date<=' . self::dic()->database()->quote($date_start->get(IL_CAL_DATETIME), "text");
		}

		if (!empty($status)) {
			$wheres[] = 'status=' . self::dic()->database()->quote($status, "integer");
		}

		if (count($wheres) > 0) {
			$sql .= ' WHERE ' . implode(" AND ", $wheres);
		}

		if ($sort_by !== null && $sort_by_direction !== null) {
			$sql .= ' ORDER BY ' . self::dic()->database()->quoteIdentifier($sort_by) . ' ' . $sort_by_direction;
		}

		if ($limit_start !== null && $limit_end !== null) {
			$sql .= ' LIMIT ' . self::dic()->database()->quote($limit_start, "integer") . ',' . self::dic()->database()->quote($limit_end, "integer");
		}

		return $sql;
	}


	/**
	 * @param int $log_id
	 *
	 * @return Log|null
	 */
	public function getLogById(int $log_id)/*: ?Log*/ {
		/**
		 * @var Log|null $log
		 */

		$log = Log::where([ "log_id" => $log_id ])->first();

		return $log;
	}


	/**
	 * @param Log $log
	 */
	public function keepLog(Log $log)/*:void*/ {
		if (!isset($this->kept_logs[$log->getStatus()])) {
			$this->kept_logs[$log->getStatus()] = [];
		}

		$this->kept_logs[$log->getStatus()][] = $log;
	}


	/**
	 * @param int $status
	 *
	 * @return Log[]
	 */
	public function getKeptLogs(int $status): array {
		if (isset($this->kept_logs[$status])) {
			return $this->kept_logs[$status];
		} else {
			return [];
		}
	}
}
