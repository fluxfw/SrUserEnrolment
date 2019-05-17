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
	 *
	 * @param string $table_name
	 * @param string $field
	 */
	public function createAutoIncrement(string $table_name, string $field)/*: void*/ ;


	/**
	 * Reset autoincrement. 1 has the effect MySQL will automatic calculate next max id
	 *
	 * @param string $table_name
	 */
	public function resetAutoIncrement(string $table_name)/*: void*/ ;
}
