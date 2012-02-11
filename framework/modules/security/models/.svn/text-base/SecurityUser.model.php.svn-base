<?php
/**
* @file
* Security User Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Security_User_Module phpAnvil_Models
*
*/

require_once(APP_PATH . 'RecordStatus.model.php');

/**
* Model for the Security User.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Security_User_Module phpAnvil_Models
*/

class SecurityUserModel extends RecordStatusModel {

    /**
    *
    *
    * @var int $id
    */

    public $id;

    /**
    *
    *
    * @var string $first_name
    */

    public $first_name;

    /**
    *
    *
    * @var string $last_name
    */

    public $last_name;

    /**
    *
    *
    * @var string $email
    */

    public $email;

    /**
    *
    *
    * @var string $username
    */

    public $username;

    /**
    *
    *
    * @var string $password
    */

    public $password;

    /**
    *
    *
    * @var string $token
    */

    public $token;


	public function __construct($atDataConnection, $id = 0)
    {
		global $phpAnvil;

        unset($this->id);
        unset($this->first_name);
        unset($this->last_name);
        unset($this->email);
        unset($this->username);
        unset($this->password);
        unset($this->token);

		$this->addProperty('id', $options[MODULE_SECURITY]['userIDField'], self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('first_name', 'first_name', self::DATA_TYPE_STRING, '', 'first_name');
		$this->addProperty('last_name', 'last_name', self::DATA_TYPE_STRING, '', 'last_name');
		$this->addProperty('email', 'email', self::DATA_TYPE_STRING, '', 'email');
		$this->addProperty('username', 'username', self::DATA_TYPE_STRING, '', 'username');
		$this->addProperty('password', 'password', self::DATA_TYPE_STRING, '', 'password');
		$this->addProperty('token', 'token', self::DATA_TYPE_STRING, '', 'token');

		parent::__construct($atDataConnection, $phpAnvil->module['Security']->usersTable, $id, '');
	}


	public function __get($name) {
		//---- Use Custom Function Override if Exists, Otherwise Return Value
		if ($name == 'name') {
			$return = $this->first_name . ' ' . $this->last_name;
		} else {
			$return = parent::__get($name);
		}

		return $return;
	}


	public function login($username = '', $password = '') {

		if (empty($username)) {
			$username = $_POST['username'];
		}

		if (empty($password)) {
			$password = $_POST['password'];
		}

		$sql = 'SELECT * FROM ' . $this->dataFrom;
		$sql .= ' WHERE username=' . $this->_dataConnection->dbString($username);
		$sql .= ' AND password=' . $this->_dataConnection->dbString($password);

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Login() completed', self::TRACE_TYPE_DEBUG);

		return $this->load($sql);
	}

	public function loadByEmail($email)
	{
		$sql = 'SELECT *';
		$sql .= ' FROM ' . $this->dataFrom;
		$sql .= ' WHERE email=' . $this->_dataConnection->dbString($email);

		return $this->load($sql);
	}

	function detect() {
		global $options;
		global $firePHP;
		$return = true;

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'User->detect()', self::TRACE_TYPE_DEBUG);
//		FB::log('User->detect()');

		//---- Is User ID Passed?
		if (isset($_COOKIE[$options[MODULE_SECURITY]['userIDCookie']])) {
			//---- Get From Cookie
			$this->id = $_COOKIE[$options[MODULE_SECURITY]['userIDCookie']];
			$msg = 'cookie = ' . $this->id;
		} elseif ($this->id != 0) {
			$msg = 'defaulting to session = ' . $this->id;
		} else {
			$msg = 'no cookie detected; session = ' . $this->id;
			$return = false;
		}

//		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'detect msg = [' . $msg . '], return =  [' . $return . ']', self::TRACE_TYPE_DEBUG);

//		FB::log('detect msg = [' . $msg . '], return =  [' . $return . ']');
		return $return;
	}

	public function saveCookie() {
		global $options;
		global $firePHP;
		if ($this->id > 0) {

			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Saving User ID Cookie...', self::TRACE_TYPE_DEBUG);
//			FB::log('Saving User ID Cookie...');

			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'User ID = ' . $this->id, self::TRACE_TYPE_DEBUG);
//			FB::log('User ID = ' . $this->id);

			setcookie($options[MODULE_SECURITY]['userIDCookie'], $this->id, time() + 60 * 60 * 24 * 365, '/');
		}
	}

	public function deleteCookie() {
		global $options;
		global $firePHP;

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Deleting User ID Cookie...', self::TRACE_TYPE_DEBUG);
//		FB::log('Deleting User ID Cookie...');

		setcookie($options[MODULE_SECURITY]['userIDCookie'], '', time() -3600, '/');
	}
}

?>