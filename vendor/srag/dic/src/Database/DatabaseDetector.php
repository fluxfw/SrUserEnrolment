<?php

namespace srag\DIC\SrUserEnrolment\Database;

use ilDBInterface;
use ilDBPdoInterface;
use srag\DIC\SrUserEnrolment\Exception\DICException;

/**
 * Class DatabaseDetector
 *
 * @package srag\DIC\SrUserEnrolment\Database
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DatabaseDetector extends AbstractILIASDatabaseDetector {

	/**
	 * @var self|null
	 */
	protected static $instance = null;


	/**
	 * @param ilDBInterface $db
	 *
	 * @return self
	 *
	 * @throws DICException DatabaseDetector only supports ilDBPdoInterface!
	 */
	public static function getInstance(ilDBInterface $db) {
		if (!($db instanceof ilDBPdoInterface)) {
			throw new DICException("DatabaseDetector only supports ilDBPdoInterface!");
		}

		if (self::$instance === null) {
			self::$instance = new self($db);
		}

		return self::$instance;
	}


	/**
	 * @inheritdoc
	 */
	public function createAutoIncrement(string $table_name, string $field)/*: void*/ {
		$this->manipulate('ALTER TABLE ' . $this->quoteIdentifier($table_name) . ' MODIFY COLUMN ' . $this->quoteIdentifier($field)
			. ' INT NOT NULL AUTO_INCREMENT');
	}


	/**
	 * @inheritdoc
	 */
	public function resetAutoIncrement(string $table_name)/*: void*/ {
		$this->manipulate('ALTER TABLE ' . $this->quoteIdentifier($table_name) . ' AUTO_INCREMENT=1');
	}
}
