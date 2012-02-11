<?php
/**
* @defgroup BP_Module Background Processor Module
* @ingroup phpAnvil_Modules
*
* @file
* Background Processor (BP) Module Include
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          BP_Module
*/

define('SQL_TABLE_BP_TASKS', $phpAnvil->db->tablePrefix . 'bp_tasks');

require_once('models/bpTask.model.php');

?>
