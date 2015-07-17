<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
/**
 * Dba Class
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @copyright	2011 - 2015 Karl Vollmer
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */

/* Make sure they aren't directly accessing it */
if (!defined('INIT_LOADED') || INIT_LOADED != '1') { exit; }

/**
 * Dba Class
 *
 * This is the database abstraction class
 * It duplicates the functionality of mysql_???
 * with a few exceptions, the row and assoc will always
 * return an array, simplifying checking on the far end
 * it will also auto-connect as needed, and has a default
 * database simplifying queries in most cases.
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */
class Dba {

	public static $stats = array('query'=>0);

	private static $_default_db;
	private static $_sql;
	private static $config;
  private static $_error;

	/**
	 * constructor
	 * This does nothing with the DBA class
	 */
	private function __construct() {

		// Rien a faire

	} // construct

	/**
	 * query
	 * This is the meat of the class this does a query, it emulates
	 * The mysql_query function
	 */
	public static function query($sql,$params=array()) {

    Event::record('SQL',$sql . " ::: " . json_encode($params),'query');
    $dbh = self::dbh();
    if (!$dbh) { 
      Event::error('Database','Error no database handle found');
      return false;
    }

    // If it's an updated query that's using params for escaping
    if (count($params)) { 
      $statement = $dbh->prepare($sql);
      $statement->execute($params);
    }
    else {
      $statement = $dbh->query($sql);
    }

		// Save the query, to make debug easier
		self::$_sql = $sql;
		self::$stats['query']++;

    // Check for errors
    if (!$statement) {
      Event::error('Database','DB Error: ' . json_encode($dbh->errorInfo()));
      self::$_error = json_encode($dbh->errorInfo());
      self::disconnect();
    }
    elseif ($statement->errorCode() && $statement->errorCode() != '00000') {
      Event::error('Database','Query Error: ' . json_encode($statement->errorInfo()));
      self::$_error = json_encode($dbh->errorInfo());
      self::finish($statement);
      self::disconnect();
      return false;
    }
    
    return $statement;

	} // query

	/**
	 * read
	 * This is a wrapper for query, it's so that in the future if we ever wanted
	 * to split reads and writes we could
	 */
	public static function read($sql,$params=array()) {

		return self::query($sql,$params);

	} // read

	/**
	 * write
	 * This is a wrapper for a write query, it is so that we can split out reads and
	 * writes if we want to
	 */
	public static function write($sql,$params=array()) {

		return self::query($sql,$params);

	} // write

	/**
	 * escape
	 * This runs a escape on a variable so that it can be safely inserted
	 * into the sql
	 */
	public static function escape($var) {

    $string = self::dbh()->quote($var);
    
    // For legacy reasons we need to remove the first and last chars (the quotes)
    // as those aren't expected by the old code
    return substr($string,1,-1);

	} // escape

	/**
	 * fetch_assoc
	 * This emulates the mysql_fetch_assoc and takes a resource result
	 * we force it to always return an array, albit an empty one
	 */
	public static function fetch_assoc($resource) {

    // If we didn't get a valid resource quit!
    if (!$resource) { return array(); }

    $result = $resource->fetch(PDO::FETCH_ASSOC);

    return $result;

		if (!$result) {
			$result = array();
		}

		return $result;

	} // fetch_assoc

	/**
	 * fetch_row
	 * This emulates the mysql_fetch_row and takes a resource result
	 * we force it to always return an array, albit an empty one
	 */
	public static function fetch_row($resource) {

    // Quit while we're ahead
    if (!$resource) { return array(); }

		$result = $resource->fetch(PDO::FETCH_NUM);

		if (!$result) {
			return array();
		}

		return $result;

	} // fetch_row

	/**
	 * num_rows
	 * This emulates the mysql_num_rows function which is really
	 * just a count of rows returned by our select statement, this
	 * doesn't work for updates or inserts
	 */
	public static function num_rows($resource) {

    if ($resource) {
      $result = $resource->rowCount();
    }

    // Force it to 0 not false
    if (!$result) {
      $result = 0;
    }

		return $result;

	} // num_rows

	/**
	 * finish
	 * This closes a result handle and clears the memory associated with it
	 */
	public static function finish($resource) {

    if ($resource) { $resource->closeCursor(); }

	} // finish

  /**
   * commit
   * Commits the current transaction
   */
  public static function commit() {

    $dbh = self::dbh();

    $retval = $dbh->commit();

    return $retval;

  } // commit

  /**
   * rollback
   * Roll the transactio back
   */
  public static function rollback() { 

      $dbh = self::dbh();

      $retval = $dbh->rollBack();

      return $retval;

  } 

	/**
	 * affected_rows
	 * This emulates the mysql_affected_rows function
	 */
	public static function affected_rows($resource) {

    $result = self::num_rows($resource);
    return $result;

	} // affected_rows

  /**
   * begin_transaction
   * Start a transaction
   */
  public static function begin_transaction() {

    $dbh = self::dbh();
   
    $retval = $dbh->beginTransaction();

    return $retval;

  } //begin_transaction

	/**
	 * _connect
	 * This connects to the database, used by the DBH function
	 */
	private static function _connect() {

		if (self::$_default_db) {
			$username = Config::get('database_username');
			$hostname = Config::get('database_hostname');
			$password = Config::get('database_password');
			$database = Config::get('database_name');
      $port     = Config::get('database_port');
		}
		else {
			// Do this later
		}

    $dsn = '';

		// We're going to use PDO so we need to create the datasource name here
		if (strpos($hostname,'/') === 0) {
      $dsn = 'mysql:unix_socket=' . $hostname;
    }
    else {
      $dsn = 'mysql:host=' . $hostname ?: 'localhost';
    }
    if ($port) {
      $dsn .= ';port=' . intval($port);
    }

    // Try and catch this
    try {
      $dbh = new PDO($dsn,$username,$password);
    }
    catch (PDOException $e) {
      self::$_error = $e->getMessage();
      Event::error('Database','Connection failed:' . $e->getMessage());
      return null;
    }

    return $dbh;
/**
		if (Config::get('sql_profiling')) {
			mysql_query('set profiling=1', $dbh);
			mysql_query('set profiling_history_size=50', $dbh);
			mysql_query('set query_cache_type=0', $dbh);
		}
		return $dbh;
**/
	} // _connect

  /**
   * _config_dbh
   * configure the charset, database and profiling for SQL
   */
  private static function _config_dbh($dbh,$database) {

    if (!$dbh) { return false; }

    // Set the charset
    $charset = self::translate_to_mysqlcharset('UTF-8');
    $charset = $charset['charset'];
    if ($dbh->exec('SET NAMES ' . $charset) === false) {
      Event::error('Database','Unable to set connection charset to ' . $charset);
    }

    if ($dbh->exec('USE `' . $database . '`') === false) {
      Event::error('Database','Unable to select database ' . $database . ' ::: ' . print_r($dbh->errorInfo(),1));
    }

    if (Config::get('sql_profiling')) { 
      $dbh->exec('SET profiling=1');
      $dbh->exec('SET profiling_history_size=50');
      $dbh->exec('SET query_cache_type=0');
    }

  } // _config_dbh

	/**
	 * show_profile
	 * This function is used for debug, helps with profiling
	 */
	public static function show_profile() {

		if (Config::get('sql_profiling')) {
			print '<br/>Profiling data: <br/>';
			$res = Dba::read('show profiles');
			print '<table>';
			while ($r = Dba::fetch_row($res)) {
				print '<tr><td>' . implode('</td><td>', $r) . '</td></tr>';
			}
			print '</table>';
		}
	} // show_profile

	/**
	 * dbh
	 * This is called by the class to return the database handle
	 * for the specified database, if none is found it connects
   * Reconnect - If true, reconnect regardless
	 */
	public static function dbh($database='',$reconnect=false) {

		if (!$database) { $database = self::$_default_db; }

		// Assign the Handle name that we are going to store
		$handle = 'dbh_' . $database;

		if (!is_object(Config::get($handle)) OR $reconnect === true) {
			$dbh = self::_connect();
			self::_config_dbh($dbh,$database);
			Config::set($handle,$dbh,true);
			return $dbh;
		}
		else {
			return Config::get($handle);
		}


	} // dbh

	/**
	 * disconnect
	 * This nukes the dbh connection based, this isn't used very often...
	 */
	public static function disconnect($database='') {

		if (!$database) { $database = self::$_default_db; }

		$handle = 'dbh_' . $database;

		// Nuke it
		Config::set($handle,null,true);

		return true;

	} // disconnect

	/**
	 * insert_id
	 * This emulates the mysql_insert_id function, it takes
	 * an optional database target
	 */
	public static function insert_id() {

		$dbh = self::dbh();
    if ($dbh) { return $dbh->lastInsertId(); }

	} // insert_id

	/**
	 * error
	 * this returns the error of the db
	 */
	public static function error() {

    return self::$_error;

	} // error

	/**
	 * auto_init
	 * This is the auto init function it sets up the config class
	 * and also sets the default database
	 */
	public static function _auto_init() {

		self::$_default_db = Config::get('database_name');

		return true;

	} // auto_init

	/**
	 * translate_to_mysqlcharset
	 * This translates the specified charset to a mysqlcharset, stupid ass mysql
	 * demands that it's charset list is different!
	 */
	public static function translate_to_mysqlcharset($charset) {

		// MySQL translte real charset names into fancy smancy MySQL land names
		switch (strtoupper($charset)) {
			case 'CP1250':
			case 'WINDOWS-1250':
				$target_charset = 'cp1250';
				$target_collation = 'cp1250_general_ci';
				break;
			case 'ISO-8859':
			case 'ISO-8859-2':
				$target_charset = 'latin2';
				$target_collation = 'latin2_general_ci';
				break;
			case 'ISO-8859-1':
			case 'CP1252':
			case 'WINDOWS-1252':
				$target_charset = 'latin1';
				$target_collation = 'latin1_general_ci';
				break;
			case 'EUC-KR':
				$target_charset = 'euckr';
				$target_collation = 'euckr_korean_ci';
				break;
			case 'CP932':
				$target_charset = 'sjis';
				$target_collation = 'sjis_japanese_ci';
				break;
			case 'KOI8-U':
				$target_charset = 'koi8u';
				$target_collation = 'koi8u_general_ci';
				break;
			case 'KOI8-R':
				$target_charset = 'koi8r';
				$target_collation = 'koi8r_general_ci';
				break;
			default;
			case 'UTF-8':
				$target_charset = 'utf8';
				$target_collation = 'utf8_unicode_ci';
				break;
		} // end mysql charset translation

		return array('charset'=>$target_charset,'collation'=>$target_collation);

	} // translate_to_mysqlcharset

	/**
	 * reset_db_charset
	 * This cruises through the database and trys to set the charset to the current
	 * site charset, this is an admin function that can be run by an administrator
	 * this can mess up data if you switch between charsets that are not overlapping
	 * a catalog verify must be re-run to correct them
	 */
	public static function reset_db_charset() {

		$translated_charset = self::translate_to_mysqlcharset(Config::get('site_charset'));
		$target_charset = $translated_charset['charset'];
		$target_collation = $translated_charset['collation'];

		// Alter the charset for the entire database
		$sql = "ALTER DATABASE `" . Config::get('database_name') . "` DEFAULT CHARACTER SET $target_charset COLLATE $target_collation";
		$db_results = Dba::write($sql);

		$sql = "SHOW TABLES";
		$db_results = Dba::read($sql);

		// Go through the tables!
		while ($row = Dba::fetch_row($db_results)) {
			$sql = "DESCRIBE `" . $row['0'] . "`";
			$describe_results = Dba::read($sql);

			// Change the tables default charset and colliation
			$sql = "ALTER TABLE `" . $row['0'] . "`  DEFAULT CHARACTER SET $target_charset COLLATE $target_collation";
			$alter_table = Dba::write($sql);

			// Itterate through the columns of the table
			while ($table = Dba::fetch_assoc($describe_results)) {
				if (
				(strpos($table['Type'], 'varchar') !== false) ||
				(strpos($table['Type'], 'enum') !== false) ||
				(strpos($table['Table'],'text') !== false)) {
					$sql = "ALTER TABLE `" . $row['0'] . "` MODIFY `" . $table['Field'] . "` " . $table['Type'] . " CHARACTER SET " . $target_charset;
					$charset_results = Dba::write($sql);
					if (!$charset_results) {
						Event::error('CHARSET','Unable to update the charset of ' . $table['Field'] . '.' . $table['Type'] . ' to ' . $target_charset);
					} // if it fails
				} // if its a varchar
			} // end columns

		} // end tables


	} // reset_db_charset

} // dba class

?>
