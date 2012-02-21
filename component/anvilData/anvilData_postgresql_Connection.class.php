<?php
/**
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
*/

require_once('anvilDataConnection.abstract.php');
require_once('anvilDataConnection.interface.php');

/**
* PostgreSQL Data Connection
*
* @version		1.0
* @date			8/3/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
*/
class anvilData_postgresql_Connection extends anvilDataConnectionAbstract implements anvilDataConnectionInterface
{
	const VERSION		= '1.0';

	const ENGINE = 'postgresql';

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


    private function _buildConnectionString()
    {
        $return = '';
        if (!empty($this->server)) {
            $return .= 'hostaddr=\'' . $this->server . '\' ';
        }

        if (!empty($this->port)) {
            $return .= 'port=\'' . $this->port . '\' ';
        } else {
            $return .= 'port=\'5432\' ';
        }

        if (!empty($this->database)) {
            $return .= 'dbname=\'' . $this->database . '\' ';
        }

        if (!empty($this->username)) {
            $return .= 'user=\'' . $this->username . '\' ';
        }

        if (!empty($this->password)) {
            $return .= 'password=\'' . $this->password . '\' ';
        }

        $return .= 'sslmode=\'disable\' ';
        $return .= 'connect_timeout=\'5\' ';

        $return = trim($return);

//        fb::log($return, 'Connection String');

        return $return;
    }

	// {{{ Properties
	private function _getConnection() {
		if(isset($this->_connection)) {
			return $this->_connection;
		}

		$this->_connection = pg_pconnect($this->_buildConnectionString());

		return $this->_connection;
	}

	public function isConnected() {
//		if(!isset($this->_connection)) {
//			$this->open(true);
//		}

        $return = false;

		if (isset($this->_connection)) {
            $status = pg_connection_status($this->_connection);

            if ($status === PGSQL_CONNECTION_BAD)
            {
                $msg = 'Database not connected.';
                fb::log($msg);
                fb::log(pg_last_error($this->_connection), 'PG Last Error');
                $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);
            } else {
                $return = true;
            }
        }

		return $return;
	}

	// }}}

	// {{{ Methods
	public function close() {
			$return = pg_close($this->_connection);
			if ($return) {
				unset($this->_connection);
			}
			return $return;
	}

	public function execute($sql) {
		$this->open(true);

        $result = pg_query($this->_connection, $sql);

        $return =  new anvilData_postgresql_Recordset($sql, $result);

        return $return;
	}

	public function open($persistent = true)
	{

		if (!isset($this->_connection) || (isset($this->_connection) && !$this->isConnected())) {

			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Opening Database Connection...');

			if (!$this->_getConnection()) {
				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to establish a database connection.');
			}

		}

        $return = $this->isConnected();

//        fb::log($return, 'Is Connected?');
        
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
				$new_str = "'" . mysql_real_escape_string($original_str, $this->_connection) . "'";
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
