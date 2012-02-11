<?php
/**
* @defgroup User_Module User Module
* @ingroup phpAnvil_Modules
*
* @file
* User Module Include
*
* @author        Nick Slevkoff <nick@slevkoff.com>
* @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup        User_Module
*
*/

define('SQL_TABLE_USERS', $options['db']['phpAnvil']['tablePrefix'] . 'users');
define('SQL_TABLE_USER_META_VALUES', $options['db']['phpAnvil']['tablePrefix'] . 'user_meta_values');
define('SQL_TABLE_USER_TYPES', $options['db']['phpAnvil']['tablePrefix'] . 'user_types');

require_once('models/User.model.php');
require_once('models/UserMetaValue.model.php');

require_once('User.module.php');

$modules[MODULE_USER] = new UserModule();

?>