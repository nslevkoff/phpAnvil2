<?php
/**
* @defgroup Session_Module Session Module
* @ingroup phpAnvil_Modules
*
* @file
* Session Module Include
*
* @author        Nick Slevkoff <nick@slevkoff.com>
* @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup        Session_Module
*
*/

define('SQL_TABLE_USER_SESSIONS', $options['db']['phpAnvil']['tablePrefix'] . 'www_sessions');

require_once('models/Session.model.php');

require_once('Session.module.php');

$modules[MODULE_SESSION] = new SessionModule();

?>