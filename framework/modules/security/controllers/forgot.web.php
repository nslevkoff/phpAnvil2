<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Forgot_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atContainer.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atImage.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to Forgot Login section for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Forgot_Module phpAnvil_Controllers
*/

class ForgotWebAction extends BaseWebAction {


	function __construct() {
		$this->requiresLogin = false;

		$return = parent::__construct();
	}


	function loadModules() {
		global $phpAnvil;


		$phpAnvil->loadWidget(MODULE_UI, 'admin_ui');
//        $phpAnvil->loadWidget(MODULE_UI, 'header');
	}


	function process() {
		global $phpAnvil, $modules;

		$showForm = true;

		$this->enableTrace();

		//---- UI
		$UI = new AdminUIWidget();
		$UI->menu->selected = MenuWidget::TAB_OPTIONS;
		$UI->template = 'dialog.tpl';
		$UI->pageIcon = 'iSecurity.png';
		$UI->pageTitle = 'Forgot Password';

		$listHTML = '';

		if(isset($_POST['btn'])) {
			if ($_POST['btn'] == 'Submit') {
//				$security = new SecurityModule();
				if ($modules[MODULE_SECURITY]->loadByEmail()) {
//					$listHTML = 'old password: ' . $modules[MODULE_SECURITY]->user->password;

					$modules[MODULE_SECURITY]->newPassword();

					$modules[MODULE_CONTENT]->page->atTemplate->assign('title', ACCOUNT_NAME);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('name', $modules[MODULE_SECURITY]->user->name);
//                    $modules[MODULE_CONTENT]->page->devTemplate->assign('username', $modules[MODULE_SECURITY]->user->username);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('password', $modules[MODULE_SECURITY]->user->password);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('url', $phpAnvil->site->webPath . 'Security/Login');

					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					// Additional headers
//                    $headers .= 'To: ' . $modules[MODULE_SECURITY]->user->name . ' <' . $modules[MODULE_SECURITY]->user->email . '>' . "\r\n";
					$from = ' <noreply@' . strtolower(ACCOUNT_NAME) . '>';
					$headers .= 'From: ' . ACCOUNT_NAME . $from . "\r\n";

					mail($modules[MODULE_SECURITY]->user->name . ' <' . $modules[MODULE_SECURITY]->user->email . '>',
						ACCOUNT_NAME . ' Forgot Password.',
						$modules[MODULE_CONTENT]->page->atTemplate->render('security/email_forgot.tpl'),
						$headers);

//					$listHTML .= '<br/>new password: ' . $modules[MODULE_SECURITY]->user->password;

					//---- Send Email

//						$phpAnvil->pageMsg->add($listHTML);

					$showForm = false;
					header('Location: ' . $phpAnvil->site->webPath . 'Security/Login');

				} else {
					$phpAnvil->errorMsg->add('No user found.');
				}
			} else {
				$showForm = false;
				header('Location: ' . $phpAnvil->site->webPath . 'Security/Login');
			}
		}


		if ($showForm) {

			//----------------- Content -----------------
//			$objContent = new atContainer('content');

	//        $objContent->addControl(new HeaderWidget());

			$UI->content->addControl(new atLiteral(null, $listHTML));

			//----------------- Form Header
			$objForm = new atForm('forgot', 'post', '', null, false);
			$objForm->innerTemplate = 'security/forgot.tpl';

			$objForm->addControl(new atEntry('email', 'email', 40, 40));

			$objForm->addControl(new atButton('submit', 'btn', atButton::TYPE_SUBMIT, 'Submit', array('class' => 'button')));
			$objForm->addControl(new atButton('cancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

			$UI->content->addControl($objForm);

//			$modules[MODULE_CONTENT]->page->addControl($objContent);
//			$logo = new atImage('logo', $phpAnvil->site->webPath . 'themes/default/images/iSecurity.png');
//			$logo->width = '48px';
//			$logo->height = '48px';
//			$modules[MODULE_CONTENT]->page->addControl($logo);
//			$modules[MODULE_CONTENT]->page->addControl(new atLiteral('title', 'Forgot Password'));
//			$modules[MODULE_CONTENT]->page->innerTemplate = 'dialog.tpl';

//			$modules[MODULE_CONTENT]->display();


			//---- Finalize and Display
			$UI->display();
		}

	}
}


$objWebAction = new ForgotWebAction();

?>