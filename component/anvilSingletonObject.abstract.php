<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/


require_once('anvilObject.abstract.php');


/**
* Base Parent Singleton Object
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class anvilSingletonObjectAbstract extends anvilObjectAbstract {
	/**
	* Version number for this class release.
	*/
	const VERSION        = '1.0';


	/**
	 * Singleton instance support.
	 *
	 */
	private static $_instance = null;


	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 */
	private function __construct() {}


	/**
	 * Singleton pattern implementation makes "clone" unavailable
	 *
	 */
	private function __clone() {}


	/**
	 * Returns an instance of the class object
	 *
	 * Singleton pattern implementation
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


}

?>