<?php
/**
 * @file
 * @author         Nick Slevkoff <nick@slevkoff.com>
 * @copyright      Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        phpAnvilTools anvilData anvilData_MySQL
 */

require_once('anvilDataConnection.abstract.php');
require_once('anvilDataConnection.interface.php');

/**
 * MySQL Data Connection
 *
 * @version         1.0
 * @date            8/25/2010
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilData anvilData_MySQL
 */
class anvilData_mysql_Connection extends anvilDataConnectionAbstract implements anvilDataConnectionInterface
{
    const VERSION = '1.0';

    const ENGINE = 'mysql';


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
        $this->dtsFormat  = 'Y-m-d H:i:s';

//        $this->enableLog();

        parent::__construct($server, $database, $username, $password,
            $persistent, $tablePrefix);

    }


    /**
     * Dynamic Get Function Override
     *
     * @param $name
     *   A string containing the name of the property to get.
     *
     * @return
     *   Value of the property.
     */
    public function __get($name)
    {
        switch ($name) {
            case 'insertID':
                $return = mysql_insert_id();
                break;

            default:
                $return = parent::__get($name);
        }

        return $return;
    }


    private function _getConnection()
    {
        if (!isset($this->_connection)) {
            $this->open();
        }

        return $this->_connection;
    }


    public function isConnected()
    {
        $result = false;

        if (isset($this->_connection)) {
            try {
                $result = mysql_ping($this->_connection);
            }
            catch (exception $e) {
                $msg = 'MySQL Error [' . mysql_errno($this->_connection) . '] ' . mysql_error($this->_connection);
                $this->_logError($msg, 'MySQL Connection Error');
//			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg);
            }
        }

        if (!$result) {
            $msg = 'Not connected to a database.';
            $this->_logWarning($msg, 'MySQL Connection');
//            $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Unable to connect to database!');
        }

        return $result;
    }


    public function close()
    {
        $return = mysql_close($this->_connection);
        if ($return) {
            unset($this->_connection);
        } else {
            $msg = 'Unable to close database!';
            $this->_logError($msg, 'MySQL Error');
        }

        return $return;
    }


    public function execute($sql)
    {

        if (!isset($this->_connection)) {
            $this->open();
        }

        $result = false;

        $this->_logVerbose($sql);

        try {
            $result = mysql_query($sql, $this->_connection);
        }
        catch (exception $e) {
            $msg = 'MySQL Error [' . mysql_errno($this->_connection) . '] ' . mysql_error($this->_connection);
            $this->_logError($msg, 'MySQL Query Error');
        }

        $return = new anvilData_mysql_Recordset($sql, $result, $this);

        return $return;
    }


    public function open()
    {
        $return = true;

        if (!isset($this->_connection) || (isset($this->_connection) && !$this->isConnected())) {


            if ($this->persistent) {
                $this->_logVerbose('Opening Persistent Database Connection...');

                $this->_connection = mysql_pconnect(
                    $this->server,
                    $this->username,
                    $this->password
                );
            } else {
                $this->_logVerbose('Opening Database Connection...');

                $this->_connection = mysql_connect(
                    $this->server,
                    $this->username,
                    $this->password
                );
            }

            if ($this->_connection) {
                mysql_select_db(
                    $this->database,
                    $this->_connection
                );
            } else {
                $return = false;
                $this->_logError('Unable to establish a database connection.', 'MySQL Connection Error');
            }


        }

        return $return;
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
