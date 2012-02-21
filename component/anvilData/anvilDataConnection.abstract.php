<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools anvilData
*/

require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';


/**
* anvilData Base Connection Abstract Class
*
* @version		1.0
* @date			10/14/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData
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
    public function __construct($server='', $database='', $username='', $password='',
        $persistent = false, $tablePrefix = '')
	{
        $this->server = $server;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->persistent = $persistent;
        $this->tablePrefix = $tablePrefix;
	}


    public function hasError()
    {
        return count($this->errorMessages) > 0;
    }

}

?>
