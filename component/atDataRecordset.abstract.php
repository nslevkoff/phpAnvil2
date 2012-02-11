<?php

require_once('atDynamicObject.abstract.php');
//require_once('atCollection.class.php');
//require_once('atData_mysql_Column.class.php');


/**
 * atData Recordset Abstract Class
 *
 * @version        1.1
 * @date            10/06/2011
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2009-2011 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools atData atData_MySQL
 */
class atDataRecordsetAbstract extends atDynamicObjectAbstract
{

    protected $_atDataConnection;
    protected $_columns;
    protected $_row;
    protected $_hasRows = false;


    public function __construct($sql = null, $result = null, $atDataConnection = null)
    {
        $this->addProperty('result', '');
        $this->addProperty('rowNumber', 0);
        $this->addProperty('sql', '');

        $this->result = $result;
        $this->sql = $sql;
        $this->_atDataConnection = $atDataConnection;

        $this->_hasRows = $result == true;

        $this->enableLog();
    }

    public function processError($number, $message = '', $detail = '')
    {
        $detail = $this->sql . $detail;

        $error_message = '<b>MySQL Error [' . $number . '] ' . $message . "</b><br><br>\n";
        $error_message .= $detail . "<br><br>\n";

//        $this->logDebug($this->_atDataConnection->errorCallback, 'errorCallback');

        $this->logError('[' . $number . '] ' . $message, 'atData Error');
        $this->logError($detail, 'atData Error Detail');

        if (isset($this->_atDataConnection->errorCallback)) {
            call_user_func($this->_atDataConnection->errorCallback, $this->_atDataConnection, $this, $number, $message, $detail);
        } elseif ($this->_atDataConnection->breakOnError) {
            trigger_error($error_message, E_USER_ERROR);
        }
    }

    public function data($column)
    {
        return $this->_row[$column];
    }


    public function hasRows()
    {
        return $this->count() > 0;
    }


    public function getRowArray()
    {
        return $this->_row;
    }


    public function toArray($rows = array())
    {
        if ($this->read()) {
            $totalColumns = $this->columns->count();

            do {
                $this->rowNumber++;

                for ($i = 0; $i < $totalColumns; $i++) {
                    array_push($rows, $this->data($i));
                }
            } while ($this->read());
        }

        return $rows;
    }


    public function columnExists($name)
    {
        return key_exists($name, $this->_row);
    }
}

?>
