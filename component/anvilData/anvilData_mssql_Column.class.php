<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools anvilData anvilData_MSSQL
*/

require_once('anvilDataColumn.abstract.php');


/**
* MS SQL Column
*
* @version		1.0
* @date			10/28/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData anvilData_MSSQL
*/
class anvilData_mssql_Column extends anvilDataColumnAbstract
{
	const VERSION	= '1.0';
	const ENGINE 	= 'mssql';

}

?>
