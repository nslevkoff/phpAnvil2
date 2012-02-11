<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData atData_MySQL
*/

require_once('atDataConnection.abstract.php');
require_once('atDataConnection.interface.php');

/**
* MySQL Data Connection
*
* @version		1.0
* @date			1/26/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData atData_MySQL
*/
class atData_array_Connection extends atDataConnectionAbstract implements atDataConnectionInterface
{
	const VERSION		= '1.0';

	const ENGINE = 'array';


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
	public function __construct()
    {
        parent::__construct();
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
				$return = 0;
				break;

			default:
				$return = parent::__get($name);
		}

		return $return;
	}


	// {{{ Properties
	private function _getConnection()
    {
        return null;
	}


	public function isConnected()
    {
        return true;
	}
	// }}}


	// {{{ Methods
	public function close()
    {
		$return = true;
		return $return;
	}


	public function execute($array)
    {
		return new atData_array_Recordset($array);
	}


	public function open()
	{
        return true;
	}


	public function dbDTS($original_dts)
	{
		if (!$original_dts)
        {
			$new_dts = 'null';
		} else {
			$new_dts = date('m/d/Y H:i:s', strtotime($original_dts));
		}
		return $new_dts;
	}


	public function dbDTS2($original_dts)
	{
		if (!$original_dts) {
			$new_dts = 'null';
		} else {
            $new_dts = date('m/d/Y H:i:s', $original_dts);
		}
		return $new_dts;
	}


	public function dbDate($original_date)
	{
		$new_date = date('m/d/Y', strtotime($original_date));
		return $new_date;
	}


	public function dbString($original_str)
	{
		if (!$original_str) {
			$new_str = "null";
		} else {
			if ($this->isConnected()) {
				$new_str = $original_str;
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
