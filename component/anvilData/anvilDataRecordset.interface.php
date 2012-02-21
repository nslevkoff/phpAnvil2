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


/**
* Recordset Interface
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData
*/
interface anvilDataRecordsetInterface
{
	public function close();
	public function count();
	public function data($column);
	public function getRowArray();
	public function hasRows();
	public function moveFirst();
	public function moveLast();
	public function moveToRow($rowNumber);
	public function read();
	public function toArray($rows = array());
}

?>
