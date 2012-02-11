<?php

/**
* @file
* Session Module
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

require_once(APP_PATH . 'Base.module.php');

/**
* 
* Session Module Class
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
class SessionModule extends BaseModule {

	const NAME			= 'Session Module';
	const CODE			= 'Session';
	const VERSION		= '4.0';
	const VERSION_BUILD = '1';
	const VERSION_DTS	= '3/9/2009 3:30:00 PM PST';

	public $session;

	function __construct() {
		$this->enableTrace();

		$return = parent::__construct();

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, self::NAME . ' v' . self::VERSION . '.' . self::VERSION_BUILD . ' loaded.');

		return $return;
	}


	function loadSession($sessionID) {
		global $phpAnvil;

		$this->session = new SessionModel($phpAnvil->db, $sessionID);
		return $this->session->load();
	}


	function processAction(Action $action) {
		global $modules, $phpAnvil;
		global $firePHP;

		$return = true;

		switch ($action->type) {
		}

		return $return;
	}


}

?>