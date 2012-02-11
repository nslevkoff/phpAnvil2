<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/


require_once('atDataObject.abstract.php');


/**
* Base Form Data Object Class
*
* This class adds form processing support to the data class.
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class atFormObjectAbstract extends atDataObjectAbstract {
	/**
	* Version number for this class release.
	*
	*/
	const VERSION		= '1.0';

	const REQUEST_TYPE_GET	= 1;
	const REQUEST_TYPE_POST = 2;
	const REQUEST_TYPE_ALL	= 3;

	protected $_formFields = array();


	public function __construct($atDataConnection, $dataFrom, $id = 0, $dataFilter = '') {
		parent::__construct($atDataConnection, $dataFrom, $id, $dataFilter);
	}


	protected function addProperty($propertyName, $dataFieldName = 'undefined', $dataFieldType = self::DATA_TYPE_NUMBER, $defaultValue = null, $formFieldName = '') {
		if (!empty($formFieldName)) {
			$this->_formFields[$propertyName] = $formFieldName;
		}
		parent::addProperty($propertyName, $dataFieldName, $dataFieldType, $defaultValue);
	}


	public function loadRequest($requestType = self::REQUEST_TYPE_POST) {
		foreach ($this->_formFields as $propertyName => $formFieldName) {
			switch ($requestType) {
				case self::REQUEST_TYPE_GET:
					if (isset($_GET[$formFieldName])) {
						$this->_values[$propertyName] = $_GET[$formFieldName];
					} elseif (!array_key_exists($propertyName, $this->_values)) {
						$this->_values[$propertyName] = $this->_properties[$propertyName];
					}
					break;
				case self::REQUEST_TYPE_POST:
					if (isset($_POST[$formFieldName])) {
						$this->_values[$propertyName] = $_POST[$formFieldName];
					} elseif (!array_key_exists($propertyName, $this->_values)) {
						$this->_values[$propertyName] = $this->_properties[$propertyName];
					}
					break;
				case self::REQUEST_TYPE_ALL:
				default:
					if (isset($_REQUEST[$formFieldName])) {
						$this->_values[$propertyName] = $_REQUEST[$formFieldName];
					} elseif (!array_key_exists($propertyName, $this->_values)) {
						$this->_values[$propertyName] = $this->_properties[$propertyName];
					}
					break;
			}
		}

		return true;
	}

}

?>