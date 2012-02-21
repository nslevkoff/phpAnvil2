<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools anvilData anvilData_MSSQL
*/

require_once('anvilDynamicObject.abstract.php');
require_once('anvilCollection.class.php');
require_once('anvilDataRecordset.interface.php');
require_once('anvilData_mssql_Column.class.php');


/**
* MS SQL Recordset
*
* @version		1.0
* @date			10/28/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData anvilData_MSSQL
*/
class anvilData_mssql_Recordset extends anvilDynamicObjectAbstract
	implements anvilDataRecordsetInterface
{
	const VERSION	= '1.0';
	const ENGINE 	= 'mssql';

	private $_columns;
	private $_row;
	private $_hasRows = false;


	/**
	* construct
	*
	* @param $sql
    *   A string containing the SQL query used for this recordset.
	* @param $result
    *
	*/
	public function __construct($sql = null, $result = null)
	{
		$this->addProperty('result', '');
		$this->addProperty('rowNumber', 0);
		$this->addProperty('sql', '');

		$this->result = $result;
		$this->sql = $sql;
//		if (mssql_get_last_message() <> '') {
//			$error_message = '<b>MS SQL Error:' . mssql_get_last_message() . "</b><br><br>\n";
//			$error_message .= $sql . "<br><br>\n";
//            fb::error($error_message);
//			trigger_error($error_message, E_USER_ERROR);
//		}

		if (!$result){
			$this->_hasRows = false;
		} else {
			$this->_hasRows = true;
		}

        return true;
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
//        global $firePHP;

		if ($propertyName == 'columns') {
			if (!isset($this->_columns)) {
				//---- Get Columns
				$this->_columns = new anvilCollection();

				$i = 0;
//                $sql = 'SHOW COLUMNS FROM ';

				while ($i < mssql_num_fields($this->result)) {
				   $meta = mssql_fetch_field($this->result, $i);
				   if ($meta) {
//                       $firePHP->_log($meta);

                       $newColumn = new anvilData_mssql_Column($meta->name, $meta->type);

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
		mssql_free_result($this->result);
	}


	public function count() {
		return mssql_num_rows($this->result);
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
			if ($this->_row = mssql_fetch_array($this->result)) {
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

			$firePHP->_log($totalColumns);

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
		return mssql_data_seek($this->result, 0);
	}


	public function moveLast() {
		$return = false;
		$totalRows = $this->count();
		if ($totalRows > 0) {
			$this->rowNumber = $totalRows - 1;
			$return = mssql_data_seek($this->result, $totalRows - 1);
		}
		return $return;
	}


	public function moveToRow($rowNumber) {
		$this->rowNumber = $rowNumber;
		return mssql_data_seek($this->result, $rowNumber);
	}
}

?>
