<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData
*/


require_once('atDynamicObject.abstract.php');


/**
* Base Dynamic Data Object Class
*
* This class adds database support to the dynamic class.
*
* @version		1.0.2
* @date			11/1/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData
*/
abstract class atDataObjectAbstract extends atDynamicObjectAbstract {
	/**
	* Version number for this class release.
	*
	*/
	const VERSION		= '1.0';

	const DATA_TYPE_BOOLEAN = 1;
	const DATA_TYPE_DATE	= 2;
	const DATA_TYPE_DTS		= 3;
	const DATA_TYPE_NUMBER	= 4;
	const DATA_TYPE_STRING	= 5;
	const DATA_TYPE_ADD_DTS	= 6;

	protected $_dataFields = array();
	protected $_dataTypes = array();
	protected $_dataConnection;

	public $dataFrom;
	public $dataFilter;
	public $nextID = 0;
	public $previousID = 0;

	public $dateFormat = '%m/%d/%Y';
	public $dtsFormat = '%m/%d/%Y %I:%M:%S';
	public $booleanFormatTrue = 'True';
	public $booleanFormatFalse = 'False';

    public $dateDefault = "'0000-00-00 00:00:00'";
    public $dtsDefault = "'0000-00-00 00:00:00'";

	protected $_isLoaded = false;


	public function __construct($atDataConnection, $dataFrom = '', $id = 0, $dataFilter = '') {
		$this->_dataConnection = $atDataConnection;
		$this->dataFrom = $dataFrom;
		$this->id = $id;
		//$this->_values['id'] = $id;
		$this->dataFilter = $dataFilter;

		parent::__construct();
	}



	protected function addProperty($propertyName, $dataFieldName = 'undefined', $dataFieldType = self::DATA_TYPE_NUMBER, $defaultValue = null) {
		$this->_dataFields[$propertyName] = $dataFieldName;
		$this->_dataTypes[$propertyName] = $dataFieldType;

		parent::addProperty($propertyName, $defaultValue);
	}


	public function count() {
		$return = 0;

		$sql = 'SELECT count(' . $this->_dataFields['id'] . ') AS total_rows FROM ' . $this->dataFrom;

		if (!empty($this->dataFilter)) {
			$sql .= ' WHERE ' . $this->dataFilter;
		}

		$objRS = $this->_dataConnection->execute($sql);
		if ($objRS->read()) {
			$return = $objRS->data('total_rows');
		}
		$objRS->close();

		return $return;
	}


    public function delete($sql = '')
    {
        $return = true;

        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = 'DELETE';
            $sql .= ' FROM ' . $this->dataFrom;
            $sql .= ' WHERE ' . $this->_dataFields['id'] . '=' . $this->id;

            if (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }

        }

        $this->_dataConnection->execute($sql);

        $this->resetProperties();
        $this->_isLoaded = false;

        return $return;
    }


	public function formatForDisplay($propertyName, $format1 = '', $format2 = '') {
		if (!array_key_exists($propertyName, $this->_values)) {
			$this->_values[$propertyName] = $this->_properties[$propertyName];
		}

		switch ($this->_dataTypes[$propertyName]) {
			case self::DATA_TYPE_BOOLEAN:
				if ($this->_values[$propertyName]) {
					if (!empty($format1)) {
						$return = $format1;
					} else {
						$return = $this->booleanFormatTrue;
					}
				} else {
					if (!empty($format2)) {
						$return = $format2;
					} else {
						$return = $this->booleanFormatFalse;
					}
				}
				break;
			case self::DATA_TYPE_DATE:
				if (empty($this->_values[$propertyName]) || strtolower($this->_values[$propertyName]) == 'null') {
					$return = $this->dateDefault;
				} else {
					if (!empty($format1)) {
						$return = strftime($format1, strtotime($this->_values[$propertyName]));
					} else {
						$return = strftime($this->dateFormat, strtotime($this->_values[$propertyName]));
					}
				}
				break;
			case self::DATA_TYPE_DTS:
			case self::DATA_TYPE_ADD_DTS:
				if (empty($this->_values[$propertyName]) || strtolower($this->_values[$propertyName]) == 'null') {
					$return = $this->dtsDefault;
				} else {
					if (!empty($format1)) {
						$return = strftime($format1, strtotime($this->_values[$propertyName]));
					} else {
						$return = strftime($this->dtsFormat, strtotime($this->_values[$propertyName]));
					}
				}
				break;
			case self::DATA_TYPE_STRING:
				$return = stripslashes($this->_values[$propertyName]);
				break;
			case self::DATA_TYPE_NUMBER:
			default:
				$return = $this->_values[$propertyName];
				break;
		}

		return $return;
	}


	public function isNew() {
		return $this->id == 0;
	}


	public function load($sql = '') {
		$return = false;
		$dataFields = '';

		//if (!$this->isNew()) {
			#---- Build SQL if Empty
			if (empty($sql)) {
				$sql = 'SELECT ';

				foreach ($this->_dataFields as $propertyName => $dataFieldName) {
					$dataFields .= ', ' . $dataFieldName;
				}
				$sql .= substr($dataFields, 2);

				$sql .= ' FROM ' . $this->dataFrom;
				$sql .= ' WHERE ' . $this->_dataFields['id'] . '=' . $this->id;

				if (!empty($this->dataFilter)) {
					$sql .= ' AND ' . $this->dataFilter;
				}

			}

			$objRS = $this->_dataConnection->execute($sql);
			if ($objRS->read()) {
				foreach ($this->_dataFields as $propertyName => $dataFieldName) {
					$this->_values[$propertyName] = $objRS->data($dataFieldName);
				}
				$return = true;
			}
			$objRS->close();


		//}
		$this->_isLoaded = $return;

		return $return;
	}


	protected function prepDataField($propertyName) {
		if (!array_key_exists($propertyName, $this->_values)) {
			$this->_values[$propertyName] = $this->_properties[$propertyName];
		}

		switch ($this->_dataTypes[$propertyName]) {
			case self::DATA_TYPE_BOOLEAN:
				$return = $this->_dataConnection->dbBoolean($this->_values[$propertyName]);
				break;
			case self::DATA_TYPE_DATE:
//				if (empty($this->_values[$propertyName])) {
                if (empty($this->_values[$propertyName]) || strtolower($this->_values[$propertyName]) == 'null') {
					$return = $this->dateDefault;
				} else {
					$return = $this->_dataConnection->dbDate($this->_values[$propertyName]);
				}
				break;
			case self::DATA_TYPE_DTS:
                if (empty($this->_values[$propertyName]) || strtolower($this->_values[$propertyName]) == 'null') {
                    $return = $this->dtsDefault;
                } else {
				    $return = $this->_dataConnection->dbDTS($this->_values[$propertyName]);
                }
				break;
			case self::DATA_TYPE_STRING:
				$return = $this->_dataConnection->dbString($this->_values[$propertyName]);
				break;
			case self::DATA_TYPE_ADD_DTS:
//				if ($this->isNew()) {
					$return = 'NOW()';
//				} else {
//					$return = $this->_dataConnection->dbDTS($this->_values[$propertyName]);
//				}
				break;
			case self::DATA_TYPE_NUMBER:
			default:
				$return = $this->_values[$propertyName] == '' ? 0 : $this->_values[$propertyName];
				break;
		}

		return $return;
	}


	public function buildSaveSQL() {
		$dataFields = '';

		if ($this->isNew()) {
			$sql = 'INSERT INTO ' . $this->dataFrom . ' (';

			foreach ($this->_dataFields as $propertyName => $dataFieldName) {
				if ($propertyName != 'id') {
					$dataFields .= ', ' . $dataFieldName;
				}
			}
			$sql .= substr($dataFields, 2);

			$sql .= ') VALUES (';

			$dataFields = '';

			foreach ($this->_dataFields as $propertyName => $dataFieldName) {
				if ($propertyName != 'id') {
					$dataFields .= ', ' . $this->prepDataField($propertyName);
				}
			}
			$sql .= substr($dataFields, 2);

			$sql .= ')';

		} else {
			$sql = 'UPDATE ' . $this->dataFrom . ' SET ';

			foreach ($this->_dataFields as $propertyName => $dataFieldName) {
				if ($propertyName != 'id' && $this->_dataTypes[$propertyName] != self::DATA_TYPE_ADD_DTS) {
					$dataFields .= ', ' . $dataFieldName . '=' . $this->prepDataField($propertyName);
				}
			}
			$sql .= substr($dataFields, 2);

			$sql .= ' WHERE ' . $this->_dataFields['id'] . '=' . $this->id;

			if (!empty($this->dataFilter)) {
				$sql .= ' AND ' . $this->dataFilter;
			}
		}

		return $sql;
	}


	public function save($sql = '', $id_sql = '') {
		$return = false;

		#---- Build SQL if Empty
		if (empty($sql)) {
			$sql = $this->buildSaveSQL();
		}

		$return = $this->_dataConnection->execute($sql);

		if ($this->isNew()) {
			if (empty($id_sql)) {
//				$id_sql = 'SELECT LAST_INSERT_ID() AS id FROM ' . $this->dataFrom;
				$id_sql = 'SELECT LAST_INSERT_ID() AS id';
			}

			$objRS = $this->_dataConnection->execute($id_sql);
			if ($objRS->read()) {
				$this->id = $objRS->data('id');
			}
		}

		return $return;
	}


	public function detectNextID($customFilter = '') {
		if ($this->id > 0) {
			$sql = 'SELECT MIN(' . $this->_dataFields['id'] . ') AS next_id FROM ' . $this->dataFrom;
			$sql .= ' WHERE ' . $this->_dataFields['id'] . '>' . $this->id;

			if (!empty($customFilter)) {
				$sql .= ' AND ' . $customFilter;
			} elseif (!empty($this->dataFilter)) {
				$sql .= ' AND ' . $this->dataFilter;
			}

			$objRS = $this->_dataConnection->execute($sql);
			if ($objRS->read()) {
				$this->nextID = $objRS->data('next_id');
			}
			$objRS->close();
		}
		return $this->nextID;
	}


	public function detectPreviousID($customFilter = '') {
		if ($this->id > 0) {
			$sql = 'SELECT MAX(' . $this->_dataFields['id'] . ') AS previous_id FROM ' . $this->dataFrom;
			$sql .= ' WHERE ' . $this->_dataFields['id'] . '<' . $this->id;

			if (!empty($customFilter)) {
				$sql .= ' AND ' . $customFilter;
			} elseif (!empty($this->dataFilter)) {
				$sql .= ' AND ' . $this->dataFilter;
			}

			$objRS = $this->_dataConnection->execute($sql);
			if ($objRS->read()) {
				$this->previousID = $objRS->data('previous_id');
			}
			$objRS->close();
		}
		return $this->previousID;
	}


	public function isLoaded() {
		return $this->_isLoaded;
	}

}

?>