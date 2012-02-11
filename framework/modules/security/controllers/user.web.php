<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       User_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
//require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
//require_once(PHPANVIL_TOOLS_PATH . 'atImage.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to load user for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        User_Module phpAnvil_Controllers
*/

class AdminUserWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadWidget(MODULE_UI, 'admin_ui');
	}


	function process() {
		global $phpAnvil, $modules;
		global $firePHP;

		$renderPage = true;
		$this->enableTrace();
		FB::log($_REQUEST);
		$i = isset($_GET['i']) ? $_GET['i'] : 0;

		$user = new AdminUserModel($phpAnvil->db, $i);
		$user->load();

		if(isset($_POST['btn'])) {

			FB::log('Form submitted...');
			FB::log($_POST['btn']);

			if ($_POST['btn'] == 'Save') {
				$user->loadRequest();

				$renderPage = false;
				$phpAnvil->actionMsg->add($user->name . ' saved.');

				if (isset($_POST['newUsername']) && $_POST['newUsername'] != $user->username) {
					$user->username = $_POST['newUsername'];
					$phpAnvil->actionMsg->add('Username updated.');
				}

				if (isset($_POST['newPassword']) && isset($_POST['newPasswordVerify'])) {
					if ($_POST['newPassword'] == $_POST['newPasswordVerify']) {
						$user->password = $_POST['newPassword'];
						$phpAnvil->actionMsg->add('Password updated. Email notification sent.');


					} else {
						$renderPage = true;
						$phpAnvil->errorMsg->add('Passwords don\'t match. Please re-enter new password again.');
					}
				}

				$user->save();
				if (!$renderPage) {
					header('Location: ' . $phpAnvil->site->webPath . 'Security/Users');
				}

			} else {
				$renderPage = false;
				header('Location: ' . $phpAnvil->site->webPath . 'Security/Users');
			}
		}

		if ($renderPage) {
			//---- UI
			$UI = new AdminUIWidget();
			$UI->menu->selected = MenuWidget::TAB_OPTIONS;
			$UI->preScript = 'js/security.js';
			$UI->pageNavText = 'All Users';
			$UI->pageNavPath = 'Security/Users';
			$UI->pageIcon = 'iAdminUser.png';
			$UI->pageTitle = 'Edit ' . $user->name;
			$UI->pageTitle = $user->name;
			$UI->pageTitleRight = 'Edit Profile';

			if ($user->isDisabled()) {
//                $UI->pageIcon = 'iSite_g.png';
				$UI->disabled = true;
				$phpAnvil->pageMsg->add('This user is currently disabled.');
			}


			//----------------- Content -----------------
			$objForm = new atForm('idEntryForm', 'post', '', null, false);
			$objForm->innerTemplate = 'editAdminUser.tpl';

			$objForm->addControl(new atHidden('idUserID', 'id', $user->id));

			$objForm->addControl(new atEntry('idName', 'name', 50, 50, $user->name));
			$objForm->addControl(new atEntry('idCompany', 'company', 50, 50, $user->company));
			$objForm->addControl(new atEntry('idRole', 'role', 20, 20, $user->role));
			$objForm->addControl(new atEntry('idEmail', 'email', 75, 128, $user->email));
			$objForm->addControl(new atEntry('idPhone', 'phone', 20, 20, $user->phone));

			if ($user->id == $modules[MODULE_SECURITY]->user->id) {
				$objForm->addControl(new atLiteral('bPassword', true));
				$objForm->addControl(new atEntry('idUsername', 'newUsername', 20, 20, $user->username));

				$objTextBox = new atEntry('idPassword', 'newPassword', 20, 20);
				$objTextBox->type = atEntry::TYPE_PASSWORD;
				$objForm->addControl($objTextBox);

				$objTextBox = new atEntry('idPasswordVerify', 'newPasswordVerify', 20, 20);
				$objTextBox->type = atEntry::TYPE_PASSWORD;
				$objForm->addControl($objTextBox);

//                $objForm->addControl(new atEntry('idPassword', 'passwordNew', 20, 20));
//                $objForm->addControl(new atEntry('idPasswordVerify', 'passwordVerify', 20, 20));

			}

			$newButton = new ActionButtonWidget('idSave', 'btnSave', '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idEntryForm\').submit();';
			$objForm->addControl($newButton);

			$newButton = new ActionButtonWidget('Cancel', 'btnCancel', $phpAnvil->site->webPath . 'Security/Users');
			$newButton->class = 'actionFormButton';
			$objForm->addControl($newButton);

//            $objForm->addControl(new atButton('save', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));

//            $objForm->addControl(new atButton('cancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));
			$UI->content->addControl($objForm);


			#========== RIGHT BAR ==========================

			if ($user->isDisabled()) {
				$UI->actions->addControl(new ActionButtonWidget('idEnableUser', 'btnEnable', '#'));
			} else {
				$UI->actions->addControl(new ActionButtonWidget('idDisableUser', 'btnDisable', '#'));
			}

			$UI->actions->addControl(new ActionButtonWidget('idDeleteUser', 'btnDelete', '#'));

			$UI->display();
		}
	}
}


$objWebAction = new AdminUserWebAction();

?>
