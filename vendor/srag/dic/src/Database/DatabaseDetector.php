<?php

namespace srag\DIC\SrUserEnrolment\Database;

use ilDBInterface;
use ilDBPdoInterface;
use ilDBPdoPostgreSQL;
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
		$table_name_q = $this->quoteIdentifier($table_name);
		$field_q = $this->quoteIdentifier($field);
		$seq_name = $table_name . "_seq";
		$seq_name_q = $this->quoteIdentifier($seq_name);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('CREATE SEQUENCE ' . $seq_name_q);

				$this->manipulate('ALTER TABLE ' . $table_name_q . ' ALTER COLUMN ' . $field_q . ' TYPE INT, ALTER COLUMN ' . $field_q
					. ' SET NOT NULL, ALTER COLUMN ' . $field_q . ' SET DEFAULT nextval(' . $seq_name_q . ')');
				break;

			default:
				$this->manipulate('ALTER TABLE ' . $table_name_q . ' MODIFY COLUMN ' . $field_q . ' INT NOT NULL AUTO_INCREMENT');
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function dropAutoIncrementTable(string $table_name)/*: void*/ {
		$seq_name = $table_name . "_seq";
		$seq_name_q = $this->quoteIdentifier($seq_name);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('DROP SEQUENCE ' . $seq_name_q);
				break;

			default:
				// Nothing to do in MySQL
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function resetAutoIncrement(string $table_name, string $field)/*: void*/ {
		$table_name_q = $this->quoteIdentifier($table_name);
		$field_q = $this->quoteIdentifier($field);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('SELECT setval(' . $table_name_q . ', (SELECT MAX(' . $field_q . ') FROM ' . $table_name_q . '))');
				break;

			default:
				$this->manipulate('ALTER TABLE ' . $table_name_q
					. ' AUTO_INCREMENT=1'); // 1 has the effect MySQL will automatic calculate next max id
				break;
		}
	}
}
