<?php

namespace srag\DIC\SrUserEnrolment\Database;

use ilDBPdoInterface;

/**
 * Interface DatabaseInterface
 *
 * @package srag\DIC\SrUserEnrolment\Database
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface DatabaseInterface extends ilDBPdoInterface {

	/**
	 * Using MySQL native autoincrement for performance
	 * Using PostgreSQL native sequence
	 *
	 * @param string $table_name
	 * @param string $field
	 */
	public function createAutoIncrement(string $table_name, string $field)/*: void*/ ;


	/**
	 * Remove PostgreSQL native sequence table
	 *
	 * @param string $table_name
	 */
	public function dropAutoIncrementTable(string $table_name)/*: void*/ ;


	/**
	 * Reset autoincrement
	 *
	 * @param string $table_name
	 * @param string $field
	 */
	public function resetAutoIncrement(string $table_name, string $field)/*: void*/ ;
}
