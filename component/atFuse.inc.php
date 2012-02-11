<?php
/**
* @file
* atFuse Include
* 
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools atFuse
*/


//---- phpAnvil Fuse
require_once('atFuseEvent.class.php');
require_once('atFuseEvent2.class.php');
require_once('atFuseTrace.class.php');
require_once('atFuseTrap.class.php');

//DevTrace::enableFullPath();
atFuseTrace::start();

$objFuseTrap = new atFuseTrap();

$objFuseTrap->onError(atFuseTrap::EVENT_TYPE_ERROR, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), '', true);
$objFuseTrap->onError(atFuseTrap::EVENT_TYPE_WARNING, array(E_WARNING, E_PARSE, E_NOTICE, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING));
$objFuseTrap->onError(atFuseTrap::EVENT_TYPE_DEBUG, array(E_STRICT));
//$objFuseTrap->applicationID = DEVTRAP_APPLICATION_ID;
//$objFuseTrap->applicationVersion = DEVTRAP_APPLICATION_VERSION;
//$objFuseTrap->serverURL = 'http://www.devtrap.com/ws.wsdl';
//$objFuseTrap->enableServer();
//$objFuseTrap->enableTrace();
//$objFuseTrap->start();


function emailDebugTrace() {
	sendDebugTrace('The function emailDebugTrace has been replaced by sendDebugTrace.');
}

function sendDebugTrace($message = '') {
	global $objFuseTrap, $options;

    fb::log($options, $options);
    
	$subject = '[' . $options['app']['code'] . '] Debug Trace';

	$objFuseTrap->sendToServer(atFuseTrap::EVENT_TYPE_DEBUG, $subject, 0, $message, __FILE__, __LINE__);
}

?>