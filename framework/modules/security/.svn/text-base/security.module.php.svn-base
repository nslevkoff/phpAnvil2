<?php
/**
*
* @file
* Security Module Controller
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          Security_Module
*/


//require_once 'Security.inc.php';

require_once PHPANVIL_FRAMEWORK_PATH . 'Base.module.php';

/**
*
* Security Module Class
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          Security_Module
*/

class SecurityModule extends BaseModule
{


	public $user;
    public $usersTable = 'users';

	function __construct() {
		global $phpAnvil;

		$this->enableTrace();

		$return = parent::__construct();

        $this->type     = self::TYPE_CORE;
        $this->name     = 'Security Module';
        $this->refName  = 'Security';
        $this->version  = '1.0';
        $this->build    = '4';

		return $return;
	}


    function init()
    {
        global $phpAnvil;

        $return = true;

        if (!$this->_isInitialized)
        {
            include_once 'models/SecurityUser.model.php';

            $phpAnvil->loadDictionary('SECURITY');

            $return = parent::init();

        }

        return $return;
    }


	function loadUser($userID) {
		global $phpAnvil;

		$this->user = new SecurityUserModel($phpAnvil->db, $userID);
		return $this->user->load();
	}

	function deleteUser(Action $action) {
		global $phpAnvil;

		if ($this->loadUser($action->data['data'])) {
			$this->user->delete();
			$this->user->save();
			$phpAnvil->actionMsg->add($this->user->name . ' has been DELETED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find user! Delete canceled.');
		}

		$return = $phpAnvil->site->webPath . 'Security/Users';

		return $return;
	}


	function disableUser(Action $action) {
		global $phpAnvil;

		if ($this->loadUser($action->data['data'])) {
			$this->user->disable();
			$this->user->save();
			$phpAnvil->actionMsg->add($this->user->name . ' has been DISABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find user! Disable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'Security/Users';

		return $return;
	}


	function enableUser(Action $action) {
		global $phpAnvil;

		if ($this->loadUser($action->data['data'])) {
			$this->user->enable();
			$this->user->save();
			$phpAnvil->actionMsg->add($this->user->name . ' has been ENABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find user! Enable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'Security/Users';

		return $return;
	}

	function login($username = '', $password = '') {
		global $phpAnvil;

		$this->loadUser(0);
//		$this->user = new AdminUserModel($phpAnvil->db);

		return $this->user->login($username, $password);
	}


	function loadByEmail($email = null) {
		global $phpAnvil;

		$return = true;

		$this->loadUser(0);
//		$this->user = new AdminUserModel($phpAnvil->db);

		if ($email == null) {
			if (array_key_exists('email', $_POST)) {
				$email = $_POST['email'];
			} else {
				$return = false;
			}
		}

		if ($return) {
			$return = $this->user->loadByEmail($email);
		}

		return $return;
	}


	function newPassword($email = null) {
		global $phpAnvil;

		$password = uniqid(rand());
		$password2 = substr($password, 0, 4);
//        $password3 = substr($password, 14, 4);
//        $password = $password2 . $password3;
		$password = $password2;

//		$this->user = new AdminUserModel($phpAnvil->db);
//		$this->user->loadByEmail($_POST['email']);
//		$return = $this->loadByEmail($_POST['email']);
		$return = $this->loadByEmail($email);

		if ($return) {
			$this->user->password = $password;
			$this->user->save();
		}

		return $return;
	}

	function getUser(Action $action) {
		global $phpAnvil;
		global $firePHP;

		$return = 0;
		$userID = $action->data['data'];
		$user = new AdminUserModel($phpAnvil->db, $userID);
		$user->load();
		if ($action->source == SOURCE_AJAX) {
		   $return = $user->toArray();
		} else {
		   $return = $user;
		}

		return $return;
	}

	function processAction(Action $action) {
		global $phpAnvil;
		global $firePHP;

		$return = true;

		switch ($action->type) {
			case ACTION_SECURITY_SESSION_LOGIN:
//                DevTrace::add(__FILE__, __METHOD__, __LINE__, 'ACTION_SECURITY_SESSION_LOGIN. Session User ID: ' . $phpAnvil->session->userID);
//                FB::log('ACTION_SECURITY_SESSION_LOGIN. Session User ID: ' . $phpAnvil->session->userID);
				if ($phpAnvil->session->userID == 0) {
//                    DevTrace::add(__FILE__, __METHOD__, __LINE__, 'Session User ID = ' . $phpAnvil->session->userID);
//                    FB::log('Session User ID = ' . $phpAnvil->session->userID);
					$this->loadUser(0);
					if ($this->user->detect()) {
//                        DevTrace::add(__FILE__, __METHOD__, __LINE__, 'User detected');
//                        FB::log('User detected');
						$phpAnvil->session->userID = $this->user->id;
						$phpAnvil->session->save();
					}
				}
//                DevTrace::add(__FILE__, __METHOD__, __LINE__, 'Returning loaded user');
//                FB::log('Returning loaded user');
				$return = $this->loadUser($phpAnvil->session->userID);
				break;

			case ACTION_GET_ADMIN_USER:
				FB::log('Security Module->ACTION_GET_ADMIN_USER');
				$return = $this->getUser($action);
//                $return = '';
				break;

			CASE ACTION_DELETE_USER:
				$return = $this->deleteUser($action);
				break;

			CASE ACTION_DISABLE_USER:
				$return = $this->disableUser($action);
				break;

			CASE ACTION_ENABLE_USER:
				$return = $this->enableUser($action);
				break;
		}

		return $return;
	}

}

$phpAnvil->module['Security'] = new SecurityModule();

?>