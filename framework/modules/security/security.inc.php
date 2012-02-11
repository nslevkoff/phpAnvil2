<?php
/**
* @defgroup Security_Module Security Module
* @ingroup phpAnvil_Modules
*
* @file
* Security Module Include
*
* @author        Nick Slevkoff <nick@slevkoff.com>
* @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup        Security_Module
*
*/

define('SQL_TABLE_SECURITY_USERS', $phpAnvil->db->tablePrefix . $phpAnvil->option['securityUsersTable']);

require_once('models/SecurityUser.model.php');


//if ($phpAnvil->mode === phpAnvil::MODE_INSTALLING_MODULE ||
//    $phpAnvil->mode === phpAnvil::MODE_REINSTALLING_MODULE)
//{
//    require_once('Security.install.php');
//} else
//{
//    require_once('Security.module.php');
//    $phpAnvil->modules['Security'] = new SecurityModule();
//}

?>
