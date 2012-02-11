<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData
*/


require_once('atObject.abstract.php');


/**
* Data Factory
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData
*/
class atDataFactory extends atObjectAbstract
{
	/**
	* Version number for this class release.
	*/
	const VERSION        = '1.0';


	/**
	* @param $traceEnabled
    *   (optional) Setting to TRUE will enable atFuse tracing. [FALSE]
	*/
	public function __construct($traceEnabled = false) {
		parent::__construct($traceEnabled);
	}


	/**
	* Creates a connection to a specific database engine.
	*
	* @param $databaseEngine
    *   A string containing the name of the database engine to use.
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
    *
	* @return
    *   Returns a atDataConnectionInterface or FALSE if unable to load the
    *   database engine.
	*/
	public function createConnection($databaseEngine, $server = '', $database = '', $username = '', $password = '', $persistent = false) {

		$databaseEngine = strtolower($databaseEngine);
		$className = 'atData_' . $databaseEngine . '_Connection';
		$fileName = 'atData_' . $databaseEngine . '.inc.php';

		if ( require_once($fileName) ) {
			return new $className($server, $database, $username, $password, $persistent);
		} else {
			echo 'ERROR: Unable to load ' . $databaseEngine . ' database engine.';
			return false;
		}
	}



}

?>