<?php
/**
* @file
* Session Database Table Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Session_Module phpAnvil_Models
*
*/

require_once(APP_PATH . 'RecordStatus.model.php');

/**
* Database table structure model for the sessions table.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Session_Module phpAnvil_Models
*/

class SessionModel extends RecordStatusModel {
    
    /**
    * 
    * 
    * @var int $id
    */
    
    public $id;
    
    /**
    * 
    * 
    * @var datetime $sessionDTS
    */
    
    public $sessionDTS;
    
    /**
    * 
    * 
    * @var string $asciiSessionID
    */
    
    public $asciiSessionID;
    
    /**
    * 
    * 
    * @var string $userIP;
    */
    
    public $userIP;
    
    /**
    * 
    * 
    * @var int $userID;
    */
    
    public $userID;
    
    /**
    * 
    * 
    * @var datetime $lastVisitDTS;
    */
    
    public $lastVisitDTS;
    
    /**
    * 
    * 
    * @var string $userAgent
    */
    
    public $userAgent;
    
    /**
    * 
    * 
    * @var string $requestURI
    */
    
    public $requestURI;
    
    /**
    * 
    * 
    * @var string $referrer
    */
    
    public $referrer;
    
    /**
    * 
    * 
    * @var int $isCookieDetected;
    */
    
    public $isCookieDetected;    


	function __construct($dataConnection, $id = 0) {
		$this->enableTrace();
        
        unset($this->id);
        unset($this->sessionDTS);
        unset($this->asciiSessionID);
        unset($this->userIP);
        unset($this->userID);
        unset($this->lastVisitDTS);
        unset($this-userAgent);
        unset($this->requestURI);
        unset($this->referrer);
        unset($this->isCookieDetected);

		$this->addProperty('id', 'session_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('sessionDTS', 'session_dts', self::DATA_TYPE_ADD_DTS, '');
		$this->addProperty('asciiSessionID', 'ascii_session_id', self::DATA_TYPE_STRING, '');
		$this->addProperty('userIP', 'user_ip', self::DATA_TYPE_STRING, '');
		$this->addProperty('userID', 'user_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('lastVisitDTS', 'last_visit_dts', self::DATA_TYPE_DTS, '');
		$this->addProperty('userAgent', 'user_agent', self::DATA_TYPE_STRING, '');
		$this->addProperty('requestURI', 'request_uri', self::DATA_TYPE_STRING, '');
		$this->addProperty('referrer', 'referrer', self::DATA_TYPE_STRING, '');
		$this->addProperty('isCookieDetected', 'is_cookie_detected', self::DATA_TYPE_BOOLEAN, false);
		$return = parent::__construct($dataConnection, SQL_TABLE_USER_SESSIONS, $id, '');

		return $return;
	}

}

?>
