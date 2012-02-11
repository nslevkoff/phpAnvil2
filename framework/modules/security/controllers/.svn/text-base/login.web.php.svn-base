<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Login_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atCheckBox.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atImage.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to Login for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Login_Module phpAnvil_Controllers
*/

class LoginWebAction extends BaseWebAction {

	function __construct() {
		$this->requiresLogin = false;

		$return = parent::__construct();
	}


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadWidget(MODULE_UI, 'ui');
//		$phpAnvil->loadModule(MODULE_SECURITY);
	}


	function process() {
		global $phpAnvil, $modules, $phrases;
		global $firePHP;

		$this->enableTrace();
//		$listHTML = '';

		FB::log($_REQUEST);
		//---- UI
		$UI = new UIWidget();
		$UI->preScript = 'js/security.js';
//		$UI->menu->selected = MenuWidget::TAB_OPTIONS;
		$UI->template = 'dialog.tpl';
		$UI->pageIcon = 'iSecurity.png';
		$UI->pageTitle = $phrases[PHRASE_GLOBAL_LOGIN];

		$showForm = true;

		//---- Process Login if Submitted
		if(isset($_POST['btn'])) {
			if ($_POST['btn'] == 'Login') {
				if (empty($_POST['username'])) {
						$phpAnvil->errorMsg->add($phrases[PHRASE_SECURITY_NO_USERNAME]);
				} else if (empty($_POST['password'])) {
						$phpAnvil->errorMsg->add($phrases[PHRASE_SECURITY_NO_PASSWORD]);
				} else {

					if ($modules[MODULE_SECURITY]->login()) {

						if (isset($_POST['remember'])) {
							$modules[MODULE_SECURITY]->user->saveCookie();
						}

						$phpAnvil->session->userID = $modules[MODULE_SECURITY]->user->id;
						$phpAnvil->session->save();

						if (isset($_SESSION['return'])) {
							header('Location: ' . $_SESSION['return']);
						} else {
							header('Location: ' . $phpAnvil->site->webPath . $phpAnvil->defaultWebModule . '/' . $phpAnvil->defaultWebAction);
						}

						$showForm = false;

					} else {
						$phpAnvil->errorMsg->add($phrases[PHRASE_SECURITY_INVALID_LOGIN]);
					}
				}
			}
		}

		if ($showForm) {

			//----------------- Content -----------------
//			$UI->content->addControl(new atLiteral(null, $listHTML));

			//---- Dictionary Phrases in Template
			$UI->assign('phrase_username', $phrases[PHRASE_SECURITY_USERNAME]);
			$UI->assign('phrase_password', $phrases[PHRASE_SECURITY_PASSWORD]);

			//----------------- Form
			$objForm = new atForm('idEntryForm', 'post', '', null, false);
			$objForm->innerTemplate = 'security/login.tpl';

			$objForm->addControl(new atEntry('idUsername', 'username', 40, 40));

			$objTextBox = new atEntry('idPassword', 'password', 20, 20);
			$objTextBox->type = atEntry::TYPE_PASSWORD;
			$objForm->addControl($objTextBox);

//			$objForm->addControl(new atLiteral('', '<br clear="all" />'));
			$objForm->addControl(new atCheckBox('idCookie', 'remember', 'remember', $phrases[PHRASE_SECURITY_SAVE_LOGIN_COOKIE]));

//			$objForm->addControl(new atButton('idLogin', 'btn', atButton::TYPE_SUBMIT, 'Login', array('class' => 'button')));

			$newButton = new ActionButtonWidget('idLogin', 'btnLogin95', $phrases[PHRASE_GLOBAL_LOGIN], '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idEntryForm\').submit();';
			$objForm->addControl($newButton);

			$objForm->addControl(new atLiteral('forgot', '<a href="forgot">' . $phrases[PHRASE_SECURITY_FORGOT_PASSWORD] . '</a>'));

			$UI->content->addControl($objForm);

			//---- Finalize and Display
			$UI->display();
		}
	}
}


$objWebAction = new LoginWebAction();

?>
