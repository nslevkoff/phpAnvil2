<?php
require_once 'anvilRSModel.abstract.php';

/**
 * @property int    $id
 * @property int    $accountID
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property int    $timezoneID
 * @property string $password
 * @property string $token
 * @property string $lastLoginDTS
 * @property int    $lastLoginSessionID
 * @property int    $supportingAccountID
 * @property int    $supportingUserID
 * @property bool   $enableDebug
 * @property string $tempToken
 * @property string $tempTokenDTS
 * @property string $tempTokenExpireDTS
 */
abstract class anvilUserModelAbstract extends anvilRSModelAbstract
{
    public $account;

    public function __construct($primaryTableName = '', $primaryFieldName = 'id')
    {
        parent::__construct($primaryTableName, $primaryFieldName);

        $this->enableLog();

        $this->fields->id->fieldName = 'user_id';
        $this->fields->id->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->token->fieldName = 'token';

        $this->fields->accountID->fieldName = 'account_id';
        $this->fields->accountID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->firstName->fieldName = 'first_name';

        $this->fields->lastName->fieldName  = 'last_name';

        $this->fields->email->fieldName = 'email';

        $this->fields->timezoneID->fieldName = 'timezone_id';
        $this->fields->timezoneID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->password->activity = false;
        $this->fields->password->fieldName = 'password';
//        $this->fields->token->fieldName    = 'token';

        $this->fields->lastLoginDTS->activity = false;
        $this->fields->lastLoginDTS->fieldName = 'last_login_dts';
        $this->fields->lastLoginDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->lastLoginSessionID->activity = false;
        $this->fields->lastLoginSessionID->fieldName = 'last_login_session_id';
        $this->fields->lastLoginSessionID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->supportingAccountID->fieldName = 'supporting_account_id';
        $this->fields->supportingAccountID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->supportingUserID->fieldName = 'supporting_user_id';
        $this->fields->supportingUserID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->enableDebug->fieldName = 'enable_debug';
        $this->fields->enableDebug->fieldType = anvilModelField::DATA_TYPE_BOOLEAN;

        $this->fields->tempToken->activity = false;
        $this->fields->tempToken->fieldName = 'temp_token';

        $this->fields->tempTokenDTS->activity = false;
        $this->fields->tempTokenDTS->fieldName = 'temp_token_dts';
        $this->fields->tempTokenDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->tempTokenExpireDTS->activity = false;
        $this->fields->tempTokenExpireDTS->fieldName = 'temp_token_expire_dts';
        $this->fields->tempTokenExpireDTS->fieldType = anvilModelField::DATA_TYPE_DTS;
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

    public function isSupporting()
    {
        return (!empty($this->supportingAccountID) || !empty($this->supportingUserID));
    }

    public function loadAccount($accountID = 0)
    {
        return true;
    }

    public function loadByLogin($email = '', $password = '', $activeOnly = true)
    {
        $sql = 'SELECT u.* FROM ' . $this->primaryTableName . ' AS u';
        $sql .= ' WHERE u.email=' . $this->dataConnection->dbString($email);
        $sql .= ' AND u.password=' . $this->dataConnection->dbString($password);

        if ($activeOnly) {
            $sql .= ' AND u.record_status_id=' . self::RECORD_STATUS_ACTIVE;
        }

        $this->_logDebug($sql);

        return $this->load($sql);
    }


    public function login($email = '', $password = '')
    {
        global $phpAnvil;

        if (empty($email)) {
            $email = $_POST['email'];
        }

        if (empty($password)) {
            $password = $phpAnvil->hash($_POST['password']);
//            $password = $_POST['password'];
        }

        $return = $this->loadByLogin($email, $password);

        return $return;
    }


    public function loadByEmail($email)
    {
        $sql = 'SELECT *';
        $sql .= ' FROM ' . $this->primaryTableName;
        $sql .= ' WHERE email=' . $this->dataConnection->dbString($email);
        $sql .= ' AND record_status_id = ' . self::RECORD_STATUS_ACTIVE;

        return $this->load($sql);
    }


    public function loadByTempToken($token = '')
    {
//        $this->_logDebug($token);

        if (empty($token)) {
            $token = $this->tempToken;
        }

        $sql = 'SELECT *';
        $sql .= ' FROM ' . $this->primaryTableName;
        $sql .= ' WHERE temp_token=' . $this->dataConnection->dbString($token);
        $sql .= ' AND record_status_id != ' . self::RECORD_STATUS_DELETED;

//        $this->_logDebug($sql);

        return $this->load($sql);
    }



    public function loadByToken($token = '')
    {
//        $this->_logDebug($token);

        if (empty($token)) {
            $token = $this->token;
        }

        $sql = 'SELECT *';
        $sql .= ' FROM ' . $this->primaryTableName;
        $sql .= ' WHERE token=' . $this->dataConnection->dbString($token);
        $sql .= ' AND record_status_id != ' . self::RECORD_STATUS_DELETED;

//        $this->_logDebug($sql);

        return $this->load($sql);
    }


    public function detect()
    {
        global $phpAnvil;

        $msg    = 'No user cookie detected.';
        $return = false;

        #---- Is User token Passed?
        if (!empty($_COOKIE[$phpAnvil->application->cookieUserID]) && !empty($_COOKIE[$phpAnvil->application->cookieUserToken])) {
            #---- Get  Cookie
            $id = $phpAnvil->decrypt($_COOKIE[$phpAnvil->application->cookieUserID]);
            $token = $phpAnvil->decrypt($_COOKIE[$phpAnvil->application->cookieUserToken]);

            if ($this->loadByToken($token) && $this->id == $id) {
                $msg = 'User Cookie Detected = [' . $this->id . '] ' . $this->token;
                $return = true;
            } else {
                $msg = 'User Cookie Token and ID Mismatched! Treating as new user...';
                $this->resetFields();
                $this->id = 0;
            }
        }

        $this->_logVerbose($msg);

        return $return;
    }


    public function saveCookie()
    {
        global $phpAnvil;

        if (!empty($this->token)) {
//            setcookie($phpAnvil->application->cookieUserID, $phpAnvil->encrypt($this->id), time() + 60 * 60 * 24 * 365, '/');
//            setcookie($phpAnvil->application->cookieUserToken, $phpAnvil->encrypt($this->token), time() + 60 * 60 * 24 * 365, '/');
            setcookie($phpAnvil->application->cookieUserID, $phpAnvil->encrypt($this->id), time() + $phpAnvil->session->innactiveTimeout, '/');
            setcookie($phpAnvil->application->cookieUserToken, $phpAnvil->encrypt($this->token), time() + $phpAnvil->session->innactiveTimeout, '/');
        }
    }


    public function deleteCookie()
    {
        global $phpAnvil;

        setcookie($phpAnvil->application->cookieUserID, '', time() - 3600, '/');
        setcookie($phpAnvil->application->cookieUserToken, '', time() - 3600, '/');
    }

    public function hashPassword($password = '')
    {
        global $phpAnvil;

        if (empty($password)) {
            $password = $this->password;
        }

        $this->password = $phpAnvil->hash($password);

        return $this->password;
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

        //---- Generate Token --------------------------------------------------
        if (empty($this->token)) {
//            $this->token = $phpAnvil->generateToken(8);

            $isUnique = false;
            $token    = '';

            while (!$isUnique) {
                $token = $phpAnvil->generateToken(8);

                //---- Verify Token is Unique
                $testSQL = 'SELECT user_id';
                $testSQL .= ' FROM ' . $this->primaryTableName;
                $testSQL .= ' WHERE token = ' . $phpAnvil->db->dbString($token);

                $testRS = $phpAnvil->db->execute($testSQL);

                $isUnique = !$testRS->read();
            }

            $this->token = $token;
        }

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