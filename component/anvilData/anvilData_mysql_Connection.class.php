<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools anvilData anvilData_MySQL
*/

require_once('anvilDataConnection.abstract.php');
require_once('anvilDataConnection.interface.php');

/**
* MySQL Data Connection
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData anvilData_MySQL
*/
class anvilData_mysql_Connection extends anvilDataConnectionAbstract implements anvilDataConnectionInterface
{
	const VERSION		= '1.0';

	const ENGINE = 'mysql';


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
        $this->dateFormat = 'Y-m-d';
        $this->dtsFormat = 'Y-m-d H:i:s';

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
			case 'insertID':
				$return = mysql_insert_id();
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

		$this->_connection = mysql_pconnect(
			$this->server,
			$this->username,
			$this->password,
			''
		);

		mysql_select_db(
			$this->database,
			$this->_connection
		);

		return $this->_connection;
	}

	public function isConnected() {
        $result = false;

		if(!isset($this->_connection)) {
			$this->open(true);
		}

		try {
			$result = mysql_ping($this->_connection);
		} catch (exception $e) {
			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'MySQL Error [' . mysql_errno($this->_connection) . '] ' . mysql_error($this->_connection));
		}

		if (!$result) {
			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to connect to database!');
		}

		return $result;
	}

	// }}}

	// {{{ Methods
	public function close() {
			$return = mysql_close($this->_connection);
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

//        echo 'Executing SQL: ' . $sql . "\n";

//        echo '.. Connection = ' . $this->_connection . ' ..' . "\n";

        $result = mysql_query($sql, $this->_connection);

//        echo '.. $result = ' . $result . " ..\n";
//        if (!$result)
//        {
//            echo '!! FAILED !!' . "\n";
//        }

//        if (!$this->_connection)
//        {
//            echo '!! No Database Connection !!' . "\n";
//        }

//		$return =  new anvilData_mysql_Recordset($sql, mysql_query($sql, $this->_connection));
        $return =  new anvilData_mysql_Recordset($sql, $result, $this);

//        echo '.. $return = ' . print_r($return, true) . ' ..' . "\n";

//        echo '..done..' . "\n";

        return $return;
	}

	public function open($persistent = true)
	{
		if (!isset($this->_connection) || (isset($this->_connection) && !$this->isConnected())) {

			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Opening Database Connection...');

            $this->_connection = mysql_pconnect(
//			$this->_connection = mysql_connect(
				$this->server,
				$this->username,
				$this->password
			);

			if (!$this->_connection) {
				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to establish a database connection.');
			}

			mysql_select_db(
				$this->database,
				$this->_connection
			);

//            mysql_query('SET SESSION wait_timeout = 60', $this->_connection);
//            $this->execute('SET SESSION wait_timeout = 60');

		}
	}

    public function dbString($value)
   	{
           $return = "null";

   		if ($value && $this->isConnected()) {
               $return = "'" . mysql_real_escape_string($value, $this->_connection) . "'";
   		}

   		return $return;
   	}


	// }}}
}


?>
