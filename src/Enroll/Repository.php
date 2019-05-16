<?php

namespace srag\Plugins\SrUserEnrolment\Enroll;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrUserEnrolment\Enroll
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository {

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
			self::$instance = new Repository();
		}

		return self::$instance;
	}


	/**
	 * Repository constructor
	 */
	private function __construct() {

	}


	/**
	 * @param Enrolled $enrolled
	 */
	protected function delete(Enrolled $enrolled)/*: void*/ {
		$enrolled->delete();
	}


	/**
	 * @return Factory
	 */
	protected function factory(): Factory {
		return Factory::getInstance();
	}


	/**
	 * @param int $rule_id
	 * @param int $object_id
	 * @param int $user_id
	 *
	 * @return Enrolled|null
	 */
	protected function getEnrolled(int $rule_id, int $object_id, int $user_id)/*: ?Enrolled*/ {
		/**
		 * @var Enrolled|null $enrolled
		 */

		$enrolled = Enrolled::where([
			"rule_id" => $rule_id,
			"object_id" => $object_id,
			"user_id" => $user_id
		])->first();

		return $enrolled;
	}


	/**
	 * @param int $rule_id
	 * @param int $object_id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function hasEnrolled(int $rule_id, int $object_id, int $user_id): bool {
		$enrolled = $this->getEnrolled($rule_id, $object_id, $user_id);

		return ($enrolled !== null);
	}


	/**
	 * @param int $rule_id
	 * @param int $object_id
	 * @param int $user_id
	 */
	public function enrolled(int $rule_id, int $object_id, int $user_id)/*: void*/ {
		$enrolled = $this->getEnrolled($rule_id, $object_id, $user_id);

		if ($enrolled === null) {
			$enrolled = $this->factory()->newInstance();
			$enrolled->setRuleId($rule_id);
			$enrolled->setObjectId($object_id);
			$enrolled->setUserId($user_id);
			$this->store($enrolled);
		}
	}


	/**
	 * @param Enrolled $enrolled
	 */
	protected function store(Enrolled $enrolled)/*: void*/ {
		$enrolled->store();
	}


	/**
	 * @param int $rule_id
	 * @param int $object_id
	 * @param int $user_id
	 */
	public function unenrolled(int $rule_id, int $object_id, int $user_id)/*: void*/ {
		$enrolled = $this->getEnrolled($rule_id, $object_id, $user_id);

		if ($enrolled !== null) {
			$this->delete($enrolled);
		}
	}
}
