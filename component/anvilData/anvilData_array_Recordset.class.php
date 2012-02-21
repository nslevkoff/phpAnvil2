<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup		phpAnvilTools anvilData anvilData_MySQL
*/

require_once('anvilDynamicObject.abstract.php');
require_once('anvilCollection.class.php');
require_once('anvilDataRecordset.interface.php');
require_once('anvilData_array_Column.class.php');


/**
* MySQL Recordset
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilData anvilData_MySQL
*/
class anvilData_array_Recordset extends anvilDynamicObjectAbstract
	implements anvilDataRecordsetInterface
{
	const VERSION	= '1.0';
	const ENGINE 	= 'array';

	private $_columns;
	private $_row;
	private $_hasRows = false;

    public $array = array();

	/**
	* construct
	*
	* @param $sql
    *   A string containing the SQL query used for this recordset.
	* @param $result
    *
	*/
	public function __construct($array)
	{
        unset($this->array);

		$this->addProperty('array', '');
		$this->addProperty('rowNumber', -1);

		$this->array = $array;

		$this->_hasRows = count($this->array);
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
				$this->_columns = new anvilCollection();

				$i = 0;

                $totalColumns = count($this->array[0]);
                $keys = array_keys($this->array[0]);

                for ($i=0; $i < $totalColumns; $i++)
                {
                    $newColumn = new anvilData_array_Column($keys[$i]);
			        $this->_columns->add($newColumn);
				}
			}
			return $this->_columns;

		} else {
			return parent::__get($propertyName);
		}
	}


	public function close()
    {
	}


	public function count()
    {
		return count($this->array);
	}


	public function data($column)
    {
		return $this->_row[$column];
	}


	public function hasRows()
    {
		return $this->count() > 0;
	}


	public function read()
    {
        $return = false;

		if (isset($this->array))
        {
//            fb::log($this->rowNumber, 'rowNumber');
//            fb::log($this->count(), 'totalRows');

            if ($this->rowNumber < ($this->count() - 1))
            {
				$this->rowNumber++;

//                fb::log($this->array);

                $this->_row = $this->array[$this->rowNumber];
				$return = true;
			}
		}

        return $return;
	}


	public function getRowArray() {
		return $this->_row;
	}


	public function toArray($rows = array())
    {
		global $firePHP;


		array_push($rows, $this->array);

		return $rows;

	}


	public function moveFirst() {
		$this->rowNumber = 0;
		return true;
	}


	public function moveLast()
    {
		$return = true;
		$totalRows = $this->count();
		if ($totalRows > 0) {
			$this->rowNumber = $totalRows - 1;
		}
		return $return;
	}


	public function moveToRow($rowNumber)
    {
		$this->rowNumber = $rowNumber;
		return true;
	}

    public function sort($column, $descending = false)
    {
//        foreach ($data as $key => $row) {
//            $sortColumn[$key]  = $row['volume'];
//            $edition[$key] = $row['edition'];
//        }

        $totalRows = $this->count();

        for ($i=0; $i < $totalRows; $i++)
        {
            $sortColumn[$i] = $this->array[$i][$column];
        }

        $data = $this->array;

        if ($descending)
        {
            array_multisort($sortColumn, SORT_DESC, $data);
        } else {
            array_multisort($sortColumn, SORT_ASC, $data);
        }
        $this->array = $data;


    }

}

?>
