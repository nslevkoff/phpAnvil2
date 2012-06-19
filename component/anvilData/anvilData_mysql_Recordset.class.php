<?php
//require_once('anvilDynamicObject.abstract.php');
require_once PHPANVIL2_COMPONENT_PATH . 'anvilCollection.class.php';
require_once 'anvilDataRecordset.abstract.php';
require_once 'anvilDataRecordset.interface.php';
require_once 'anvilData_mysql_Column.class.php';


/**
* MySQL Recordset
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData anvilData_MySQL
*/
class anvilData_mysql_Recordset extends anvilDataRecordsetAbstract
	implements anvilDataRecordsetInterface
{
	const VERSION	= '1.0';
	const ENGINE 	= 'mysql';


	/**
	* construct
	*
	* @param $sql
    *   A string containing the SQL query used for this recordset.
	* @param $result
    *
	*/
	public function __construct($sql = null, $result = null, $anvilDataConnection = null)
	{
        parent::__construct($sql, $result, $anvilDataConnection);

//        $this->addProperty('result', '');
//        $this->addProperty('rowNumber', 0);
//        $this->addProperty('sql', '');

//        $this->result = $result;
//        $this->sql = $sql;

		if (mysql_errno()) {
            $this->processError(mysql_errno(), mysql_error());

//			$error_message = '<b>MySQL Error [' . mysql_errno() . '] ' . mysql_error() . "</b><br><br>\n";
//			$error_message .= $sql . "<br><br>\n";
			//$error_message .= "<b>Trace:</b><br>\n" . DevTrap::renderTraceInfoHTML() . "\n<br><br>\n";
//			trigger_error($error_message, E_USER_ERROR);
//            trigger_error($error_message, E_RECOVERABLE_ERROR);
		}

//	    $this->_hasRows = $result;
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

				while ($i < mysql_num_fields($this->result)) {
				   $meta = mysql_fetch_field($this->result, $i);
				   if ($meta) {
//                       $firePHP->_log($meta);

                       $newColumn = new anvilData_mysql_Column($meta->name, $meta->type);

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
        parent::close();

		mysql_free_result($this->result);
	}


	public function count() {
		return mysql_num_rows($this->result);
	}


//	public function data($column) {

		//echo('data(' . $column . ")<br>\n");

//		return $this->_row[$column];
//	}


//	public function hasRows() {
		//return $this->_hasRows;
//		return $this->count() > 0;
//	}


	public function read() {
		if ($this->result) {
			if ($this->_row = mysql_fetch_array($this->result)) {
				$this->rowNumber++;
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


//	public function getRowArray() {
//		return $this->_row;
//	}


//	public function toArray($rows = array()) {
//		global $firePHP;


//		if ($this->read()) {
//			$totalColumns = $this->columns->count();

//			$firePHP->_log($totalColumns);

//			do {
//				$this->rowNumber++;

//				for ($i=0; $i<$totalColumns; $i++) {
//					array_push($rows, $this->data($i));
//				}
//			} while($this->read());
//		}
//
//		return $rows;
//
//	}


	public function moveFirst() {
		$this->rowNumber = 0;
		return mysql_data_seek($this->result, 0);
	}


	public function moveLast() {
		$return = false;
		$totalRows = $this->count();
		if ($totalRows > 0) {
			$this->rowNumber = $totalRows - 1;
			$return = mysql_data_seek($this->result, $totalRows - 1);
		}
		return $return;
	}


	public function moveToRow($rowNumber) {
		$this->rowNumber = $rowNumber;
		return mysql_data_seek($this->result, $rowNumber);
	}

//    public function columnExists($name)
//    {
//        return key_exists($name, $this->_row);
//    }
}

?>
