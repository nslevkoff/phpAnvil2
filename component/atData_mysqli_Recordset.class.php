<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools atData atData_MySQL
*/

require_once('atDynamicObject.abstract.php');
require_once('atCollection.class.php');
require_once('atDataRecordset.interface.php');
require_once('atData_mysqli_Column.class.php');


/**
* MySQLi Recordset
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atData atData_MySQL
*/
class atData_mysqli_Recordset extends atDynamicObjectAbstract
	implements atDataRecordsetInterface
{
	const VERSION	= '1.0';
	const ENGINE 	= 'mysqli';

	private $_atConnection;
	private $_columns;
	private $_row;
	private $_hasRows = false;


	/**
	* construct
	*
	* @param string $sql SQL query used for this recordset.
	* @param string $result ?
	*/
	public function __construct($atConnection = null, $sql = null, $result = null)
	{
		$this->addProperty('result', '');
		$this->addProperty('rowNumber', 0);
		$this->addProperty('sql', '');

		$this->_atConnection = $atConnection;

		$this->result = $result;
		$this->sql = $sql;
		if (mysqli_errno($this->_atConnection->connection)) {
			$error_message = '<b>MySQL Error [' . mysqli_errno($this->_atConnection->connection) . '] ' . mysqli_error($this->_atConnection->connection) . "</b><br><br>\n";
			$error_message .= $sql . "<br><br>\n";
			trigger_error($error_message, E_USER_ERROR);
		}

		if (!$result){
			$this->_hasRows = false;
		} else {
			$this->_hasRows = true;
		}
	}


	/**
	* Dynamic Get Function Override
	*
	* @param $name
    *   A string containing the name of the property to get.
	* @return
    *   Value of the property.
	*/
	public function __get($propertyName)
	{
        global $firePHP;

		if ($propertyName == 'columns') {
			if (!isset($this->_columns)) {
				//---- Get Columns
				$this->_columns = new atCollection();

				$i = 0;
				$numFields = mysqli_num_fields($this->result);
				while ($i < $numFields) {
//				   $meta = mysqli_fetch_field($this->result, $i);
				   $meta = mysqli_fetch_field($this->result);
				   if ($meta) {
//						$this->_columns->add(new atData_mysqli_Column($meta->name, $meta->type));
                       $firePHP->log($meta);

                       $newColumn = new atData_mysqli_Column($meta->name, $meta->type);

                       $this->_columns->add($newColumn);
				   }
				   $i++;
				}
			}
			return $this->_columns;

		} else {
			return parent::__get($propertyName);
		}
	}


	public function close() {
		mysqli_free_result($this->result);
	}


	public function count() {
		return mysqli_num_rows($this->result);
	}


	public function data($column) {

		//echo('data(' . $column . ")<br>\n");

		return $this->_row[$column];
	}


	public function hasRows() {
		//return $this->_hasRows;
		return $this->count() > 0;
	}


	public function read() {
		if ($this->result) {
			if ($this->_row = mysqli_fetch_array($this->result)) {
				$this->rowNumber++;
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public function getRowArray() {
		return $this->_row;
	}


	public function toArray($rows = array()) {
		global $firePHP;


		if ($this->read()) {
			$totalColumns = $this->columns->count();

			$firePHP->log($totalColumns);

			do {
				$this->rowNumber++;

				for ($i=0; $i<$totalColumns; $i++) {
					array_push($rows, $this->data($i));
				}
//				array_push($rows, $this->data(0));
//				array_push($rows, $this->data(1));
			} while($this->read());
		}

		return $rows;

	}


	public function moveFirst() {
		$this->rowNumber = 0;
		return mysqli_data_seek($this->result, 0);
	}


	public function moveLast() {
		$return = false;
		$totalRows = $this->count();
		if ($totalRows > 0) {
			$this->rowNumber = $totalRows - 1;
			$return = mysqli_data_seek($this->result, $totalRows - 1);
		}
		return $return;
	}


	public function moveToRow($rowNumber) {
		$this->rowNumber = $rowNumber;
		return mysqli_data_seek($this->result, $rowNumber);
	}
}

?>
