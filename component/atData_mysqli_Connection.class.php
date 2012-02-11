<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData atData_MySQLi
*/

require_once('atDataConnection.abstract.php');
require_once('atDataConnection.interface.php');


/**
* MySQLi Data Connection
*
* @version		1.1
* @date			10/14/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData atData_MySQLi
*/
class atData_mysqli_Connection extends atDataConnectionAbstract implements atDataConnectionInterface
{
	const VERSION	= '1.0';
	const ENGINE    = 'mysqli';

//    const FORMAT_DATE = 'Y-m-d';
//    const FORMAT_DTS = 'Y-m-d H:i:s';

    public $dateFormat = 'Y-m-d';
    public $dtsFormat = 'Y-m-d H:i:s';


//	private $_connection;

//    public $server;
//    public $database;
//    public $username;
//    public $password;
//    public $persistent;

//    public $tablePrefix;


	/**
	* construct
	*
	* @param $server
    *   A string containing the IP or URL to the database server.
	* @param $database
    *   A string containing the name of the database for this connection.
	* @param $username
    *   A string containing the username for the connection's security login.
	* @param $password
    *   A string containing the password for the connection's security login.
	* @param $persistent
    *   (optional) Setting to TRUE will enable persistent connections. [FALSE]
	*/
    public function __construct($server, $database, $username, $password,
        $persistent = false, $tablePrefix = '')
    {
        parent::__construct($server, $database, $username, $password,
            $persistent, $tablePrefix);

//        unset($this->server);
//        unset($this->database);
//        unset($this->username);
//        unset($this->password);
//        unset($this->persistent);

//		$this->addProperty('server', 'localhost');
//		$this->addProperty('database', '');
//		$this->addProperty('username', '');
//		$this->addProperty('password', '');
//		$this->addProperty('persistent', false);

//		$this->server = $server;
//		$this->database = $database;
//		$this->username = $username;
//		$this->password = $password;
//		$this->persistent = $persistent;
	}


	/**
	* Dynamic Get Function Override
	*
	* @param $name
    *   A string containing the name of the property to get.
	* @return
    *   Value of the property.
	*/
	public function __get($name) {
		switch ($name) {
			case 'connection':
				$return = $this->_getConnection();
				break;

			case 'insertID':
				$return = mysqli_insert_id();
				break;

			default:
				$return = parent::__get($name);
		}

		return $return;
	}



	// {{{ Properties
	private function _getConnection() {
		if(isset($this->_connection)) {
			return $this->_connection;
		}

		$this->_connection = mysqli_connect(
			$this->server,
			$this->username,
			$this->password,
			''
		);

		mysqli_select_db(
			$this->_connection,
			$this->database
		);

		return $this->_connection;
	}

	public function isConnected($attemptOpen = false) {
		if(!isset($this->_connection)) {
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Connection not established yet.');
			$this->open(true);
		}

		try {
			$result = mysqli_ping($this->_connection);
		} catch (exception $e) {
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'MySQL Error [' . mysqli_errno($this->_connection) . '] ' . mysqli_error($this->_connection));
		}

		if (!$result) {
			if ($attemptOpen) {
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Attempting to re-establish database connection...');
				$this->open(true);

				try {
					$result = mysqli_ping($this->_connection);
				} catch (exception $e) {
					$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'MySQL Error [' . mysqli_errno($this->_connection) . '] ' . mysqli_error($this->_connection));
				}

				if (!$result) {
					$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to connect to database!');
				}
			} else {
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to connect to database!');
			}
		}

		return $result;
	}


//	public function getInsertID() {
		//return mysql_insert_id($this->_connection);
//		return mysqli_insert_id($this->_connection);
//	}

	// }}}

	// {{{ Methods
	public function close() {
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Closing Database Connection...');
			$return = mysqli_close($this->_connection);
			if ($return) {
				unset($this->_connection);
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Database Connection Closed.');
			}
			return $return;
	}

	public function execute($sql) {
		$this->open(true);

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $sql);

//		$return = new atData_mysqli_Recordset($this->_connection, $sql, mysqli_query($this->_connection, $sql));
		$return = new atData_mysqli_Recordset($this, $sql, mysqli_query($this->_connection, $sql));

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Done.');

		return $return;
	}

	public function executeMulti($sql) {
		$this->open(true);
//		return new atData_mysqli_Recordset($this->_connection, $sql, mysqli_multi_query($this->_connection, $sql));
		return new atData_mysqli_Recordset($this, $sql, mysqli_multi_query($this->_connection, $sql));
	}

	public function open($persistent = true) {
		if (!isset($this->_connection) || (isset($this->_connection) && !$this->isConnected())) {

			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Opening Database Connection...');

			$this->_connection = mysqli_pconnect(
				$this->server,
				$this->username,
				$this->password,
				''
			);

			if (!$this->_connection) {
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to establish a database connection.');
			}

			mysqli_select_db(
				$this->_connection,
				$this->database
			);

//            $this->execute('SET SESSION wait_timeout = 60');

		}
	}


	public function dbDTS($original_dts)
	{
		if (!$original_dts) {
			$new_dts = 'null';
		} else {
			$new_dts = "'" . date('Y-m-d H:i:s', strtotime($original_dts)) . "'";
		}
		//$new_dts = $this->_dbh->DBDate($original_dts);
		return $new_dts;
	}

	public function dbDTS2($original_dts)
	{
		if (!$original_dts) {
			$new_dts = 'null';
		} else {
			$new_dts = "'" . date('Y-m-d H:i:s', $original_dts) . "'";
		}
		//$new_dts = $this->_dbh->DBDate($original_dts);
		return $new_dts;
	}

	public function dbDate($original_date)
	{
		$new_date = "'" . date('Y-m-d', strtotime($original_date)) . "'";
		//$new_dts = $this->_dbh->DBDate($original_dts);
		return $new_date;
	}

	public function dbString($original_str)
	{
		$new_str = $original_str;
		if (!$original_str) {
			$new_str = "null";
		} else {
			if ($this->isConnected(true)) {
				$new_str = "'" . mysqli_real_escape_string($this->_connection, $original_str) . "'";
			} else {
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'No database connection, using original string...');
			}
		}

		return $new_str;
	}

	public function dbBoolean($value) {
		if ($value) {
			return 1;
		} else {
			return 0;
		}
	}

	// }}}
}


?>
