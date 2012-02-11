<?php
/**
* @file
* User Meta Value Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        User_Meta_Module phpAnvil_Models
*
*/

require_once(APP_PATH . 'RecordStatus.model.php');

/**
* Model for the User Meta Value.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        User_Meta_Module phpAnvil_Models
*/

class UserMetaValueModel extends RecordStatusModel {
    
    /**
    * 
    * 
    * @var int $id;
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
    * @var int $promotionID
    */

    public $promotionID;
    
    /**
    * 
    * 
    * @var int $promotionItemID
    */

    public $promotionItemID;
    
    /**
    * 
    * 
    * @var int $sessionID
    */

    public $sessionID;
    
    /**
    * 
    * 
    * @var int $userID
    */

    public $userID;
    
    /**
    * 
    * 
    * @var int $userMetaNameID
    */

    public $userMetaNameID;
    
    /**
    * 
    * 
    * @var string $name
    */

    public $name;
    
    /**
    * 
    * 
    * @var string $value
    */

    public $value;
    
    
	function __construct($dataConnection, $id = 0) {
		$this->enableTrace();
        
        unset($this->id);
        unset($this->addDTS);
        unset($this->promotionID);
        unset($this->promotionItemID);
        unset($this->sessionID);
        unset($this->userID);
        unset($this->userMetaNameID);
        unset($this->name);
        unset($this->value);

		$this->addProperty('id', 'user_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('addDTS', 'add_dts', self::DATA_TYPE_ADD_DTS, '', 'addDTS');
		$this->addProperty('promotionID', 'promotion_id', self::DATA_TYPE_NUMBER, 0, 'promotionID');
		$this->addProperty('promotionItemID', 'promotion_item_id', self::DATA_TYPE_NUMBER, 0, 'promotionItemID');
		$this->addProperty('sessionID', 'session_id', self::DATA_TYPE_NUMBER, 0, 'sessionID');
		$this->addProperty('userID', 'user_id', self::DATA_TYPE_NUMBER, 0, 'userID');
		$this->addProperty('userMetaNameID', 'user_meta_name_id', self::DATA_TYPE_NUMBER, 0, 'userMetaNameID');
		$this->addProperty('name', 'name', self::DATA_TYPE_STRING, '', 'name');
		$this->addProperty('value', 'value', self::DATA_TYPE_STRING, '', 'value');

		$return = parent::__construct($dataConnection, SQL_TABLE_USER_META_VALUES, $id, '');

		return $return;
	}


}

?>
