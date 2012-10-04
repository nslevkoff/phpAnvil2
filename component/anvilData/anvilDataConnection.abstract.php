<?php
/**
 * @file
 * @author         Nick Slevkoff <nick@slevkoff.com>
 * @copyright      Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        phpAnvilTools anvilData
 */

require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';


/**
 * anvilData Base Connection Abstract Class
 *
 * @version         1.0
 * @date            10/14/2010
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilData
 */
abstract class anvilDataConnectionAbstract extends anvilObjectAbstract
{
    public $_connection;

    public $server = 'localhost';
    public $port;
    public $database;
    public $username;
    public $password;
    public $persistent = false;

    public $tablePrefix;


    public $errorMessages = array();

    public $breakOnError = true;

    public $errorCallback;

    public $dateFormat = 'Y-m-d';
    public $dtsFormat = 'Y-m-d H:i:s';


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
    public function __construct($server = '', $database = '', $username = '', $password = '',
                                $persistent = false, $tablePrefix = '')
    {
        $this->server      = $server;
        $this->database    = $database;
        $this->username    = $username;
        $this->password    = $password;
        $this->persistent  = $persistent;
        $this->tablePrefix = $tablePrefix;
    }


    public function hasError()
    {
        return count($this->errorMessages) > 0;
    }


    public function dbDTS($value, $format = 'Y-m-d H:i:s')
    {
        $return = "null";

        if ($value) {
            $return = "'" . date($format, strtotime($value)) . "'";
        }
        return $return;
    }


    public function dbDTS2($value, $format = 'Y-m-d H:i:s')
    {
        $return = "null";

        if ($value) {
            $return = "'" . date($format, $value) . "'";
        }
        return $return;
    }


    public function dbDate($value)
    {
        $return = "'" . date('Y-m-d', strtotime($value)) . "'";
        return $return;
    }


    public function dbFloat($value)
    {
        $return = "null";

        if ($value) {
            $return = floatval($value);
        }

        return $return;
    }


    public function dbNow($anvilRegional)
    {
        $now    = new DateTime(null, $anvilRegional->dateTimeZone);
        $return = "'" . $now->format($anvilRegional->dtsFormat) . "'";

        return $return;
    }


    public function dbNumber($value)
    {
        $return = "null";

        if ($value) {
            $return = intval($value);
        }

        return $return;
    }


    public function dbString($value)
    {
        $return = "null";

        if ($value && $this->isConnected()) {
            $return = "'" . $value . "'";
        }

        return $return;
    }


    public function dbBoolean($value)
    {
        if ($value) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param $sql string
     * @return anvilDataRecordsetAbstract
     */
    public function execute($sql)
    {
    }

    public function isConnected()
    {
        return isset($this->_connection);
    }

    public function open()
    {
        return true;
    }
}

?>
