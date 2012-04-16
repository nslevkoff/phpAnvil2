<?php
require_once 'anvilRSModel.abstract.php';

/**
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $token
 */
abstract class anvilAccountModelAbstract extends anvilRSModelAbstract
{
    public function __construct($primaryTableName = '', $primaryFieldName = 'id')
    {
        parent::__construct($primaryTableName, $primaryFieldName);

        $this->enableLog();

        $this->fields->id->fieldName = 'account_id';
        $this->fields->id->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->token->fieldName = 'token';

        $this->fields->name->fieldName = 'name';

        $this->fields->email->fieldName = 'email';
    }


    public function disable()
    {

        global $phpAnvil;

        $return = parent::disable();

        if ($return) {
            $phpAnvil->loadAllCustomModules();
            $phpAnvil->triggerEvent('account.disabled', array($this->id));
        }

        return $return;
    }


    public function enable()
    {

        global $phpAnvil;

        $return = parent::enable();

        if ($return) {
            $phpAnvil->loadAllCustomModules();
            $phpAnvil->triggerEvent('account.enabled', array($this->id));
        }

        return $return;
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


    function detect()
    {
        global $phpAnvil;

        $msg    = 'No account cookie detected.';
        $return = false;

        #---- Is User token Passed?
        if (!empty($_COOKIE[$phpAnvil->application->cookieAccountToken])) {
            #---- Get  Cookie
            $token = $phpAnvil->decrypt($_COOKIE[$phpAnvil->application->cookieAccountToken]);

            if ($this->loadByToken($token)) {
                $msg = 'Account Cookie Detected = ' . $this->token;
                $return = true;
            }
        }

        $this->_logVerbose($msg);

        return $return;
    }


    public function saveCookie()
    {
        global $phpAnvil;

        if (!empty($this->token)) {
            setcookie($phpAnvil->application->cookieAccountToken, $phpAnvil->encrypt($this->token), time() + 60 * 60 * 24 * 365, '/');
        }
    }


    public function deleteCookie()
    {
        global $phpAnvil;

        setcookie($phpAnvil->application->cookieAccountToken, '', time() - 3600, '/');
    }

    public function save($sql = '', $id_sql = '')
    {
        global $phpAnvil;

        //---- Save New Status for Event Trigger
        $isNew = $this->isNew();

        //---- Generate Token --------------------------------------------------
        if (empty($this->token)) {
            $this->token = $phpAnvil->generateToken(8);
        }

        //---- Save the Record
        $return = parent::save($sql, $id_sql);

        //---- Trigger Event
        if ($return) {
            $phpAnvil->loadAllCustomModules();
            if ($isNew) {
                $phpAnvil->triggerEvent('account.added', array($this->id));
            } else {
                $phpAnvil->triggerEvent('account.updated', array($this->id));
            }

        }

        return $return;
    }

}


?>