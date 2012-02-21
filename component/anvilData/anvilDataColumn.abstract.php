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

require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';


/**
* anvilData Base Column Abstract Class
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData
*/
abstract class anvilDataColumnAbstract extends anvilDynamicObjectAbstract
{
	/**
	* Version number for this class release.
	*
	*/
	const VERSION	= '1.0';


    public $name;
    public $default;
    public $maxLength;
    public $allowNull = true;
    public $primaryKey = false;
    public $uniqueKey = false;
    public $multipleKey = false;
    public $numeric = false;
    public $type;


	/**
	* construct
	*
	* @param $name
    *   A string containing the name of the column.
	* @param $type
    *   An integer indicating the data type for the column.
	*/
	public function __construct($name, $type = '')
	{
		$this->name = $name;
		$this->type = $type;
	}
}

?>
