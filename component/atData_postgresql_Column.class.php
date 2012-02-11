<?php
/**
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
*/

require_once('atDataColumn.abstract.php');


/**
* PostgreSQL Column
*
* @version		1.0
* @date			8/3/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
*/
class atData_postgresql_Column extends atDataColumnAbstract
{
	const VERSION	= '1.0';
	const ENGINE 	= 'postgresql';

}

?>
