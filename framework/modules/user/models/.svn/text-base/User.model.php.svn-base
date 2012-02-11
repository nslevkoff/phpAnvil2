<?php

/**
* @file
* User Database Table Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        User_Module phpAnvil_Models
*
*/

require_once(APP_PATH . 'RecordStatus.model.php');

/**
* Database table structure model for the user table.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        User_Module phpAnvil_Models
*/

class UserModel extends RecordStatusModel {
	const TYPE_VISITOR	= 10;
	const TYPE_OPTIN	= 20;
	const TYPE_CUSTOMER = 30;

	const ERROR_NONE					= 0;
	const ERROR_MISSING_OPTIN_DATA 		= 1;
	const ERROR_MISSING_CUSTOMER_DATA 	= 2;
	const ERROR_MISSING_OPTIN_ACTIVITY 	= 3;
	const ERROR_MISSING_ORDER 			= 4;
	const ERROR_UNKNOWN_ACQUISITION 	= 5;
	const ERROR_TOO_OLD					= 6;
    
    /**
    * 
    * 
    * @var int $id
    */
    
    public $id;
    
    /**
    * 
    * 
    * @var datetime $addDTS
    */
    
    public $addDTS;
    
    /**
    * 
    * 
    * @var int $addSourceID
    */
    
    public $addSourceID;
    
    /**
    * 
    * 
    * @var int $typeID
    */
    
    public $typeID;
    
    /**
    * 
    * 
    * @var string $name
    */
    
    public $name;
    
    /**
    * 
    * 
    * @var string $firstName
    */
    
    public $firstName;
    
    /**
    * 
    * 
    * @var string $lastName
    */
    
    public $lastName;
    
    /**
    * 
    * 
    * @var string $email
    */
    
    public $email;
    
    /**
    * 
    * 
    * @var string $company
    */
    
    public $company;
    
    /**
    * 
    * 
    * @var string $workPhone
    */
    
    public $workPhone;
    
    /**
    * 
    * 
    * @var string homePhone
    */
    
    public $homePhone;
    
    /**
    * 
    * 
    * @var string $fax
    */
    
    public $fax;
    
    /**
    * 
    * 
    * @var string $address1
    */
    
    public $address1;
    
    /**
    * 
    * 
    * @var string $address2
    */
    
    public $address2;
    
    /**
    * 
    * 
    * @var string $city
    */
    
    public $city;
    
    /**
    * 
    * 
    * @var string $state
    */
    
    public $state;
    
    /**
    * 
    * 
    * @var string $zip
    */
    
    public $zip;
    
    /**
    * 
    * 
    * @var datetime $acquisitionDTS
    */
    
    public $acquistionDTS;
    
    /**
    * 
    * 
    * @var int $acquisitionSessionID
    */
    
    public $acquisitionSessionID;
    
    /**
    * 
    * 
    * @var string $acquisitionPartnerCode
    */
    
    public $acquisitionPartnerCode;
    
    /**
    * 
    * 
    * @var int $acquisitionAdID
    */
    
    public $acquisitionAdID;
    
    /**
    * 
    * 
    * @var int $acquisitionPromotionID
    */
    
    public $acquisitionPromotionID;
    
    /**
    * 
    * 
    * @var datetime $recentDTS
    */
    
    public $recentDTS;
    
    /**
    * 
    * 
    * @var int $recentSessionID
    */
    
    public $recentSessionID;
    
    /**
    * 
    * 
    * @var int $recentAdID
    */
    
    public $recentAdID;
    
    /**
    * 
    * 
    * @var int $recentPromotionID
    */
    
    public $recentPromotionID;
    
    /**
    * 
    * 
    * @var bool $isFullfillmentDisabled
    */
    
    public $isFullfillmentDisabled;
    
    /**
    * 
    * 
    * @var int $dtAffiliateID
    */
    
    public $dtAffiliateID;
    
    /**
    * 
    * 
    * @var int $dtCampaign
    */
    
    public $dtCampaignID;
    
    /**
    * 
    * 
    * @var int $dtBannerID
    */
    
    public $dtBannerID;

//	public $userProcessStatusID = 0;

//	public $userErrorID = 0;
//	public $audited = false;


	function __construct($dataConnection, $id = 0) {
		$this->enableTrace();
        
        unset($this->id);
        unset($this->addDTS);
        unset($this->addSourceID);
        unset($this->typeID);
        unset($this->name);
        unset($this->firstName);
        unset($this->lastName);
        unset($this->email);
        unset($this->company);
        unset($this->workPhone);
        unset($this->homePhone);
        unset($this->fax);
        unset($this->address1);
        unset($this->address2);
        unset($this->city);
        unset($this->state);
        unset($this->zip);
        unset($this->country);
        
        unset($this->acquisitionDTS);
        unset($this->acquisitionSessionID);
        unset($this->acquisitionPartnerCode);
        unset($this->acquisitionAdID);
        unset($this->acquisitionPromotionID);
        
        unset($this->recentDTS);
        unset($this->recentSessionID);
        unset($this->recentAdID);
        unset($this->recentPromotionID);
        
        unset($this->isFullfillmentDisabled);
        
        unset($this->dtAffiliateID);
        unset($this->dtCampaignID);
        unset($this->dtBannerID);

		$this->addProperty('id', 'user_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('addDTS', 'add_dts', self::DATA_TYPE_ADD_DTS, '', 'addDTS');
		$this->addProperty('addSourceID', 'add_source_id', self::DATA_TYPE_NUMBER, 0, 'addSourceID');
		$this->addProperty('typeID', 'user_type_id', self::DATA_TYPE_NUMBER, self::TYPE_VISITOR, 'typeID');
		$this->addProperty('name', 'name', self::DATA_TYPE_STRING, '', 'name');
		$this->addProperty('firstName', 'first_name', self::DATA_TYPE_STRING, '');
		$this->addProperty('lastName', 'last_name', self::DATA_TYPE_STRING, '');
		$this->addProperty('email', 'email', self::DATA_TYPE_STRING, '', 'email');
		$this->addProperty('company', 'company', self::DATA_TYPE_STRING, '', 'company');
		$this->addProperty('workPhone', 'work_phone', self::DATA_TYPE_STRING, '', 'workPhone');
		$this->addProperty('homePhone', 'home_phone', self::DATA_TYPE_STRING, '', 'homePhone');
		$this->addProperty('fax', 'fax', self::DATA_TYPE_STRING, '', 'fax');
		$this->addProperty('address1', 'address1', self::DATA_TYPE_STRING, '', 'address1');
		$this->addProperty('address2', 'address2', self::DATA_TYPE_STRING, '', 'address2');
		$this->addProperty('city', 'city', self::DATA_TYPE_STRING, '', 'city');
		$this->addProperty('state', 'state', self::DATA_TYPE_STRING, '', 'state');
		$this->addProperty('zip', 'zip', self::DATA_TYPE_STRING, '', 'zip');
		$this->addProperty('country', 'country', self::DATA_TYPE_STRING, '', 'country');

		$this->addProperty('acquisitionDTS', 'acquisition_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'acquisitionDTS');
		$this->addProperty('acquisitionSessionID', 'acquisition_session_id', self::DATA_TYPE_NUMBER, 0, 'acquisitionSessionID');
		$this->addProperty('acquisitionPartnerCode', 'acquisition_partner_code', self::DATA_TYPE_STRING, '');
		$this->addProperty('acquisitionAdID', 'acquisition_ad_id', self::DATA_TYPE_NUMBER, 0, 'acquisitionAdID');
		$this->addProperty('acquisitionPromotionID', 'acquisition_promotion_id', self::DATA_TYPE_NUMBER, 0, 'acquisitionPromotionID');

		$this->addProperty('recentDTS', 'recent_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'recentDTS');
		$this->addProperty('recentSessionID', 'recent_session_id', self::DATA_TYPE_NUMBER, 0, 'recentSessionID');
		$this->addProperty('recentAdID', 'recent_ad_id', self::DATA_TYPE_NUMBER, 0, 'recentAdID');
		$this->addProperty('recentPromotionID', 'recent_promotion_id', self::DATA_TYPE_NUMBER, 0, 'recentPromotionID');

		$this->addProperty('isFulfillmentDisabled', 'is_fulfillment_disabled', self::DATA_TYPE_BOOLEAN, false);

		$this->addProperty('dtAffiliateID', 'dt_affiliate_id', self::DATA_TYPE_NUMBER, 0, 'dtAffiliateID');
		$this->addProperty('dtCampaignID', 'dt_campaign_id', self::DATA_TYPE_NUMBER, 0, 'dtCampaignID');
		$this->addProperty('dtBannerID', 'dt_banner_id', self::DATA_TYPE_NUMBER, 0, 'dtBannerID');

		$return = parent::__construct($dataConnection, SQL_TABLE_USERS, $id, '');

		return $return;
	}


	public function getUserTypeName() {
		switch ($this->_typeID) {

			case self::TYPE_VISITOR:
				return 'Visitor';
				break;
			case self::TYPE_OPTIN:
				return 'Optin';
				break;
			case self::TYPE_CUSTOMER:
				return 'Customer';
				break;
		}
	}


	function detect() {
		$return = true;

		//---- Is PM3 User ID Passed?
		if (isset($_REQUEST[URL_USER_ID])) {

			//---- Get From URL
			$this->id = $_REQUEST[URL_USER_ID];
			$msg = 'request = ' . $this->id;

		} elseif (isset($_COOKIE[COOKIE_USER_ID])) {

			//---- Get From PM3 Cookie
			$this->id = $_COOKIE[COOKIE_USER_ID];
			$msg = 'cookie = ' . $this->id;

//		} elseif (isset($_COOKIE[COOKIE_USER_ID_OLD])) {

//			//---- Get From Old Cookie
//			$this->id = $_COOKIE[COOKIE_USER_ID_OLD];
//			$msg = 'cookie = ' . $this->id;

		} elseif(isset($_REQUEST['affid'])) {

			//---- Get From URL
			$this->id = $_REQUEST['affid'];
			$msg = 'affid = ' . $this->id;

		} elseif ($this->id != 0) {
			$msg = 'defaulting to session = ' . $this->id;

		} else {
			$msg = 'no cookie detected; session = ' . $this->id;
			$return = false;
		}

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'no cookie detected; session = ' . $this->id, self::TRACE_TYPE_DEBUG);
		return $return;
	}


	//---- Load the Data Record from the DB
	public function loadByEmail($email = '') {
		$return = false;

		$sql = 'SELECT * FROM ' . SQL_TABLE_USERS;
		if (!empty($email)) {
			$sql .= ' WHERE email=' . $this->_dataConnection->dbString($email);
		} else {
			$sql .= ' WHERE email=' . $this->_dataConnection->dbString($this->email);
		}

		$return = $this->load($sql);

		return $return;
	}


	public function saveCookie() {
		if ($this->id > 0) {

			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Saving User ID Cookie...', self::TRACE_TYPE_DEBUG);
			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'User ID = ' . $this->id, self::TRACE_TYPE_DEBUG);

			setcookie(COOKIE_USER_ID, $this->id, time() + 60 * 60 * 24 * 365, '/');
//			setcookie(COOKIE_USER_ID_OLD, $this->id, time() + 60 * 60 * 24 * 365, '/');
		}
	}
}

?>
