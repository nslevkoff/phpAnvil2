<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData atData_MSSQL
*/

require_once('atDataConnection.abstract.php');
require_once('atDataConnection.interface.php');

/**
* MS SQL Data Connection
*
* @version		1.0
* @date			10/28/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData atData_MSSQL
*/
class atData_mssql_Connection extends atDataConnectionAbstract implements atDataConnectionInterface
{
	const VERSION		= '1.0';

	const ENGINE = 'mssql';

    const FORMAT_DATE = 'm-d-Y';
    const FORMAT_DTS = 'm-d-Y H:i:s';

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

//        unset($this->tablePrefix);

//		$this->addProperty('server', 'localhost');
//		$this->addProperty('database', '');
//		$this->addProperty('username', '');
//		$this->addProperty('password', '');
//		$this->addProperty('persistent', false);
//        $this->addProperty('tablePrefix', '');

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
//			case 'insertID':
//				$return = mssql_insert_id();
//				break;

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

		$this->_connection = mssql_pconnect(
			$this->server,
			$this->username,
			$this->password);

		mssql_select_db(
			$this->database,
			$this->_connection
		);

		return $this->_connection;
	}

	public function isConnected() {
		if(!isset($this->_connection)) {
			$this->open(true);
		}

		try {
            $result = true;
//			$result = mysql_ping($this->_connection);
		} catch (exception $e) {
            $msg = 'MSSQL Error: ' . mssql_get_last_message() . '.';
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);
		}

		if (!$result) {
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to connect to database!');
		}

		return $result;
	}

	// }}}

	// {{{ Methods
	public function close() {
			$return = mssql_close($this->_connection);
			if ($return) {
				unset($this->_connection);
			}
			return $return;
	}

	public function execute($sql) {
		$this->open(true);

		//echo($sql . "<br><br>\n");
//		$from_name = 'Nick';
//		$from_address = 'nick@devuture.com';
//		$recipients = 'nick@devuture.com';
//		$subject = '[DevData] SQL Execute';
//		$message = $sql;
//		$headers = "From: DevData <no-reply@devuture.com>\n";
//		mail($recipients, $subject, $message, $headers);

		return new atData_mssql_Recordset($sql, mssql_query($sql, $this->_connection));
	}

	public function open($persistent = true)
	{
        $return = false;

		if (!isset($this->_connection) || (isset($this->_connection) && !$this->isConnected())) {

            $msg = 'Opening Database Connection...';
            fb::info($msg);
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);

            $this->_connection = mssql_connect (
				$this->server,
				$this->username,
				$this->password
			);


			if (!$this->_connection)
            {
                $errorMsg = mssql_get_last_message();
                if (empty($errorMsg))
                {
                    $errorMsg = 'Unable to connect to server: ' . $this->server;
                }
                $this->errorMessages[] = $errorMsg;

                $msg = 'Unable to establish a database connection.';
                fb::error($msg);
				$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);
			} else {

			    $return = mssql_select_db(
				    $this->database,
				    $this->_connection
			    );

                $lastErrorMsg = mssql_get_last_message();
                if (!$return)
                {
                    $msg = 'Unable to select the database, ' . $this->database . '...';
                    fb::error($msg);
                    $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);

                    $this->errorMessages[] = $lastErrorMsg;

                }
            }
		}

        return $return;
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
		if (!$original_str) {
			$new_str = "null";
		} else {
			if ($this->isConnected()) {
//				$new_str = "'" . mysql_real_escape_string($original_str, $this->_connection) . "'";
                $new_str = "'" . stripslashes(eregi_replace("'", "''", $original_str)) . "'";
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
