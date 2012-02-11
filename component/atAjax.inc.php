<?php
/**
* @file
* phpAnvilTools Ajax Include
*
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools atAjax
*/

require('atAjax.class.php');

$objAjax = new atAjax();
//$agent = new Agent;
if (isset($_POST['ata_afunc'])) $ata_afunc = $_POST['ata_afunc']; else $ata_afunc="";
if (isset($_POST['ata_sfunc'])) $ata_sfunc = $_POST['ata_sfunc']; else $ata_sfunc="";
if (isset($_POST['ata_event'])) $ata_event = $_POST['ata_event']; else $ata_event="";
if (isset($_POST['ata_cfunc'])) $ata_cfunc = $_POST['ata_cfunc']; else $ata_cfunc="";
if (isset($_POST['ata_sfunc_args'])) $ata_sfunc_args = $_POST['ata_sfunc_args']; else $ata_sfunc_args="";

if ($_SERVER['REQUEST_URI'] == null||$_SERVER['REQUEST_URI']=="") {
  $ata_url = $_SERVER['PHP_SELF'];
} else {
  $ata_url = $_SERVER['REQUEST_URI'];
}

if($ata_afunc=="call") {
  $objAjax->call($ata_sfunc, $ata_cfunc, $ata_sfunc_args);
}

if($ata_afunc=="listen") {
  $objAjax->listen($ata_event, $ata_cfunc, $ata_sfunc_args);
}


?>