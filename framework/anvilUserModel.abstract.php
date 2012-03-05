<?php
require_once 'anvilRSModel.abstract.php';

/**
 * @property int        $accountID
 * @property string     $firstName
 * @property string     $lastName
 * @property string     $email
 * @property int        $timezoneID
 * @property string     $password
 * @property string     $token
 * @property string     $lastLoginDTS
 * @property int        $lastLoginSessionID
 * @property int        $spyUserID
 * @property boolean    $enableDebug
 */
abstract class anvilUserModelAbstract extends anvilRSModelAbstract
{

    public function __construct($primaryTableName = '', $primaryFieldName = 'id')
    {
        parent::__construct($primaryTableName, $primaryFieldName);

        $this->fields->id->fieldName = 'user_id';
        $this->fields->id->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->accountID->fieldName = 'account_id';
        $this->fields->accountID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->firstName->fieldName = 'first_name';
        $this->fields->lastName->fieldName  = 'last_name';

        $this->fields->email->fieldName = 'email';

        $this->fields->timezoneID->fieldName = 'timezone_id';
        $this->fields->timezoneID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->password->fieldName = 'password';
        $this->fields->token->fieldName    = 'token';

        $this->fields->lastLoginDTS->fieldName = 'last_login_dts';
        $this->fields->lastLoginDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->lastLoginSessionID->fieldName = 'last_login_session_id';
        $this->fields->lastLoginSessionID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->spyUserID->fieldName = 'spy_user_id';
        $this->fields->spyUserID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->enableDebug->fieldName = 'enable_debug';
        $this->fields->enableDebug->fieldType = anvilModelField::DATA_TYPE_BOOLEAN;

    }


    public function disable()
    {

        global $phpAnvil;

        $return = parent::disable();

        if ($return) {
            $phpAnvil->loadAllCustomModules();
            $phpAnvil->triggerEvent('user.disabled', array($this->accountID, $this->id));
        }

        return $return;
    }


    public function enable()
    {

        global $phpAnvil;

        $return = parent::enable();

        if ($return) {
            $phpAnvil->loadAllCustomModules();
            $phpAnvil->triggerEvent('user.enabled', array($this->accountID, $this->id));
        }

        return $return;
    }


    public function loadByLogin($email = '', $password = '', $activeOnly = true)
    {
        $sql = 'SELECT u.* FROM ' . $this->primaryTableName . ' AS u';
        $sql .= ' WHERE u.email=' . $this->dataConnection->dbString($email);
        $sql .= ' AND u.password=' . $this->dataConnection->dbString($password);

        if ($activeOnly) {
            $sql .= ' AND u.record_status_id=' . self::RECORD_STATUS_ACTIVE;
        }

        return $this->load($sql);
    }


    public function login($email = '', $password = '')
    {

        if (empty($email)) {
            $email = $_POST['email'];
        }

        if (empty($password)) {
            //            $password = $this->encrypt($_POST['password']);
            $password = $_POST['password'];
        }

        $return = $this->loadByLogin($email, $password);

        return $return;
    }


    public function loadByEmail($email)
    {
        $sql = 'SELECT *';
        $sql .= ' FROM ' . $this->dataFrom;
        $sql .= ' WHERE email=' . $this->dataConnection->dbString($email);

        return $this->load($sql);
    }


    function detect()
    {
        global $phpAnvil;

        $return = true;

        #---- Is User ID Passed?
        if (isset($_COOKIE[$phpAnvil->application->cookieUserID])) {
            #---- Get From Cookie
            $this->id = $_COOKIE[$phpAnvil->application->cookieUserID];
            $msg      = 'cookie = ' . $this->id;
        } elseif ($this->id != 0) {
            $msg = 'defaulting to session = ' . $this->id;
        } else {
            $msg    = 'no cookie detected; session = ' . $this->id;
            $return = false;
        }

        $this->_logVerbose($msg);

        return $return;
    }


    public function saveCookie()
    {
        global $phpAnvil;

        if ($this->id > 0) {
            setcookie($phpAnvil->application->cookieUserID, $this->id, time() + 60 * 60 * 24 * 365, '/');
        }
    }


    public function deleteCookie()
    {
        global $phpAnvil;

        setcookie($phpAnvil->application->cookieUserID, '', time() - 3600, '/');
    }


    public function encrypt($value)
    {
        return md5(utf8_encode($value));
    }


    public function encryptPassword()
    {
        $this->password = $this->encrypt($this->password);
    }


    public function generatePassword()
    {
        global $phpAnvil;

        $this->password = $phpAnvil->generateToken(8);

        return $this->password;
    }


    public function save($sql = '', $id_sql = '')
    {
        global $phpAnvil;

        //---- Save New Status for Event Trigger
        $isNew = $this->isNew();

        //---- Save the Record
        $return = parent::save($sql, $id_sql);

        //---- Trigger Event
        if ($return) {
            $phpAnvil->loadAllCustomModules();
            if ($isNew) {
                $phpAnvil->triggerEvent('user.added', array($this->accountID, $this->id));
            } else {
                $phpAnvil->triggerEvent('user.updated', array($this->accountID, $this->id));
            }

        }

        return $return;
    }

}


?>