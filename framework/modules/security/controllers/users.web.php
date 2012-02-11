<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Users_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atPanel.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLink.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atList.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to load user for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Users_Module phpAnvil_Controllers
*/

class AdminUsersWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadWidget(MODULE_UI, 'admin_ui');
		$phpAnvil->loadWidget(MODULE_UI, 'admin_user');
		$phpAnvil->loadWidget(MODULE_UI, 'grid');
		$phpAnvil->loadWidget(MODULE_UI, 'panel');
	}


	function process() {
		global $phpAnvil, $modules;
		global $firePHP;

		$renderPage = true;
		$this->enableTrace();
		FB::log($_REQUEST);

		if(isset($_POST['btn'])) {
			if ($_POST['btn'] == 'Save') {
				if ($_POST['name'] != '' && $_POST['email'] != '') {
					$user = new AdminUserModel($phpAnvil->db, $_POST['id']);
					$user->load();
					$user->loadRequest();

					$name = explode(' ', $_POST['name']);
					$un = $name[0];

					$user->username = $un;

					$password = uniqid(rand());
					$password2 = substr($password, 0, 4);
//                    $password3 = substr($password, 14, 4);
//                    $password = $password2 . $password3;
					$password = $password2;
					$user->password = $password;
					$user->save();


					$modules[MODULE_CONTENT]->page->atTemplate->assign('title', ACCOUNT_NAME);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('name', $user->name);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('username', $user->username);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('password', $user->password);
					$modules[MODULE_CONTENT]->page->atTemplate->assign('url', $phpAnvil->site->webPath . 'Security/Logon');

					/*$objDevEmail = new DevEmail();
					$objDevEmail->libraryPath = SWIFT_PATH;
					$objDevEmail->enableTrace();
					$objDevEmail->open();*/

					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					// Additional headers
//                    $headers .= 'To: ' . $user->name . ' <' . $user->email . '>' . "\r\n";
					$from = '<noreply@' . strtolower(ACCOUNT_NAME) . '>';
					$headers .= 'From: ' . ACCOUNT_NAME . $from . "\r\n";

					mail($user->name . ' <' . $user->email . '>', ACCOUNT_NAME . ' Permission Granted.', $modules[MODULE_CONTENT]->page->atTemplate->render('email_newuser.tpl'), $headers);
					#-- send email
					/*$objDevEmail->simpleSend(
					ACCOUNT_NAME, 'no_reply@' . strtolower(ACCOUNT_NAME),
					$user->name, $user->email,
					ACCOUNT_NAME. ', Security Request',
					$modules[MODULE_CONTENT]->page->devTemplate->render('email_newuser.tpl'),
					'text/html');*/

					$phpAnvil->actionMsg->add('Admin User Saved. Email notification sent with random username and password.');

				} else {
					$phpAnvil->errorMsg->add('Missing user\'s name and/or email address.');
				}

			}
		}
		if ($renderPage) {
			//---- UI
			$UI = new AdminUIWidget();
			$UI->menu->selected = MenuWidget::TAB_OPTIONS;
			$UI->pageIcon = 'iAdminUsers.png';
			$UI->pageTitle = 'Admin Users';
			$UI->preScript = 'js/security.js';

			$UI->pageNavText = 'Options Menu';
			$UI->pageNavPath = 'FCG/Options';

			//----------------- Content -----------------
			//----------------- SQL
			$sql = 'SELECT * FROM ' . SQL_TABLE_ADMIN_USER;
			$objRS = $phpAnvil->db->execute($sql);

			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$objGridWidget = new GridWidget();
					$objGridWidget->class = 'adminUserWidget';
					if ($objRS->data('record_status_id') == AdminUserModel::RECORD_STATUS_DISABLED) {
						$objGridWidget->disabled = true;
					}

					$objGridWidget->icon = 'iAdminUser.png';
					$objGridWidget->linkURL = 'Security/User?i=' . $objRS->data('admin_user_id');
					$objGridWidget->title = $objRS->data('name');

					if ($objRS->data('role') != '') {
						$objGridWidget->addControl(new atLiteral('', "Role: " . $objRS->data('role') . "<br />\n"));
					}
					if ($objRS->data('company') != '') {
						$objGridWidget->addControl(new atLiteral('', "Company: " . $objRS->data('company') . "<br />\n"));
					}


					$content = '<div class="extra">';
					if ($objRS->data('email') != '') {
						$content .= '<a href="mailto:' . $objRS->data('email') . '"><span class="bulletEmail"></span></a>';
					}

					if ($objRS->data('phone') != '') {
						$content .= '<span class="bulletPhone">' . $objRS->data('phone') . '</span>';
					}
					$content .= '</div>';
					if (!empty($content)) {
						$objGridWidget->addControl(new atLiteral('', $content));
					}

					$UI->content->addControl($objGridWidget);


					//$userWidget = new AdminUserWidget();
//                    $userWidget->user->id = $objRS->data('admin_user_id');
//                    $userWidget->user->name = $objRS->data('name');
//                    $userWidget->user->company = $objRS->data('company');
//                    $userWidget->user->role = $objRS->data('role');
//                    $userWidget->user->email = $objRS->data('email');
//                    $userWidget->user->phone = $objRS->data('phone');
//                    if ($objRS->data('record_status_id') == AdminUserModel::RECORD_STATUS_DISABLED) {
//                        $userWidget->disabled = true;
//                    }
//                    $UI->content->addControl($userWidget);
				}
			}

			//----------------- RIGHT -----------------
			//----------------- Edit -----------------
			$newButton = new ActionButtonWidget('idAddUser', 'btnNewUser', '#');
			$UI->actions->addControl($newButton);
//            $UI->rightColumn->addControl(new atLink('idAddItem', '[add admin user]', '#', 'idAddItem'));

			//----- New Admin User
			$panel = new PanelWidget('idPanelEdit', 'New User', 'panelEdit');
			$objatForm = new atForm('idForm', 'post', '', array('class' => 'idForm'), false);
			$objatForm->addControl(new atHidden('idUserID', 'id', '0'));

			$objatForm->innerTemplate = 'eAdminUser.tpl';

			$objatForm->addControl(new atEntry('idName', 'name', 20, 50));
//            $objatForm->addControl(new atEntry('idCompany', 'company', 20, 50));
//            $objatForm->addControl(new atEntry('idRole', 'role', 20, 50));
			$objatForm->addControl(new atEntry('idEmail', 'email', 30, 128));
//            $objatForm->addControl(new atEntry('idPhone', 'phone', 20, 20));
//            $objatForm->addControl(new atEntry('idUsername', 'username', 20, 50));
//            $objatForm->addControl(new atEntry('idPassword', 'password', 20, 50));

			/*$objTextBox = new atEntry('idPassword', 'password', 20, 50);
			$objTextBox->type = atEntry::TYPE_PASSWORD;
			$objatForm->addControl($objTextBox);*/

//            $objatForm->addControl(new atButton('idSave', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));

//            $objatForm->addControl(new atButton('idCancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

			$newButton = new ActionButtonWidget('idSave', 'btnSave', '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idForm\').submit();';
			$objatForm->addControl($newButton);

			$newButton = new ActionButtonWidget('idCancel', 'btnCancel', '#');
			$newButton->class = 'actionFormButton';
			$objatForm->addControl($newButton);

			$panel->addControl($objatForm);
			$UI->actions->addControl($panel);



			//----------------- See Also -----------------
			$panel = new PanelWidget(null, 'See Also...', 'panelRight');
			$objList = new atList(null, atList::TYPE_BULLET, 'menuRight');
			$objList->addControl(new atLink(null, 'Admin Users', $phpAnvil->site->webPath . 'Security/Users', 'bulletUser'));
			$objList->addControl(new atLink(null, 'Modules', $phpAnvil->site->webPath . 'phpAnvil/Modules', 'bulletModule'));
			$objList->addControl(new atLink(null, 'Sites', $phpAnvil->site->webPath . 'Site/Sites', 'bulletSite'));
			$panel->addControl($objList);
			$UI->rightColumn->addControl($panel);

			//---- Finalize and Display
			$UI->display();
		}
	}
}


$objWebAction = new AdminUsersWebAction();

?>