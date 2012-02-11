<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Logout_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to Login for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Logout_Module phpAnvil_Controllers
*/

class LogoutWebAction extends BaseWebAction {


	function __construct() {
		$this->requiresLogin = false;

		$return = parent::__construct();
	}


	function loadModules() {
		global $phpAnvil;


		$phpAnvil->loadWidget(MODULE_UI, 'ui');
	}


	function process() {
		global $phpAnvil, $modules, $phrases;

		$phpAnvil->session->abandon();
		$modules[MODULE_SECURITY]->user->resetProperties();
		$modules[MODULE_SECURITY]->user->deleteCookie();

		$phpAnvil->actionMsg->add($phrases[PHRASE_SECURITY_LOGGED_OUT]);

		//---- UI
		$UI = new UIWidget();
		$UI->menu->selected = MenuWidget::TAB_OPTIONS;
		$UI->template = 'dialog.tpl';
		$UI->pageIcon = 'iSecurity.png';
		$UI->pageTitle = $phrases[PHRASE_GLOBAL_LOGOUT];


		//----------------- Form

		$newButton = new ActionButtonWidget('idLogon', 'btnLogin95', $phrases[PHRASE_GLOBAL_LOGIN], $phpAnvil->site->webPath . 'Security/Login');
		$newButton->class = 'actionFormButton';
		$UI->content->addControl($newButton);

		$UI->content->addControl(new atLiteral(null, '<br /><br /><br />'));


		//---- Finalize and Display
		$UI->display();

	}
}


$objWebAction = new LogoutWebAction();

?>
