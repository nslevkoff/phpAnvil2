<?php
require_once PHPANVIL2_FRAMEWORK_PATH . 'anvilModelField.class.php';

require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';
//require_once('anvilCollection.class.php');
//require_once('anvilData_mysql_Column.class.php');


/**
 * anvilData Recordset Abstract Class
 *
 * @version        1.1
 * @date            10/06/2011
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2009-2011 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilData anvilData_MySQL
 */
class anvilDataRecordsetAbstract extends anvilObjectAbstract
{

    protected $_anvilDataConnection;
    protected $_columns;
    protected $_row;
    protected $_hasRows = false;

    public $result;
    public $rowNumber = 0;
    public $sql;

    public function __construct($sql = null, $result = null, $anvilDataConnection = null)
    {
        $this->result = $result;
        $this->sql = $sql;
        $this->_anvilDataConnection = $anvilDataConnection;

        $this->_hasRows = $result == true;

        $this->enableLog();
    }

    public function processError($number, $message = '', $detail = '')
    {
        $detail = $this->sql . $detail;

        $error_message = '<b>MySQL Error [' . $number . '] ' . $message . "</b><br><br>\n";
        $error_message .= $detail . "<br><br>\n";

//        $this->_logDebug($this->_anvilDataConnection->errorCallback, 'errorCallback');

        $this->_logError('[' . $number . '] ' . $message, 'anvilData Error');
        $this->_logError($detail, 'anvilData Error Detail');

        if (isset($this->_anvilDataConnection->errorCallback)) {
            call_user_func($this->_anvilDataConnection->errorCallback, $this->_anvilDataConnection, $this, $number, $message, $detail);
        } elseif ($this->_anvilDataConnection->breakOnError) {
            trigger_error($error_message, E_USER_ERROR);
        }
    }

    public function data($column, $dataType = 0)
    {
        global $phpAnvil;

        $return = $this->_row[$column];

        if ($dataType > 0) {
            $regional = $phpAnvil->regional;
            $value = $return;

            switch ($dataType) {
                case anvilModelField::DATA_TYPE_DATE:

                    if (!empty($value) && strtolower($value) != 'null') {
                        if (isset($regional->dateTimeZone)) {
                            $dateTime = new DateTime($value, $regional->dateTimeZone);
                            $return   = $dateTime->format($regional->dateFormat);
                        } else {
                            $dateTime = new DateTime($value, new DateTimeZone('PST'));
                            $return   = $dateTime->format($regional->dateFormat);
                        }
                    }
                    break;

                case anvilModelField::DATA_TYPE_DTS:
                case anvilModelField::DATA_TYPE_ADD_DTS:
                    if (!empty($value) && strtolower($value) != 'null') {
                        if (isset($regional->dateTimeZone)) {
                            $dateTime = new DateTime($value, $regional->dateTimeZone);
                            $return   = $dateTime->format($regional->dtsFormat);
                        } else {
                            $dateTime = new DateTime($value, new DateTimeZone('PST'));
                            $return   = $dateTime->format($regional->dtsFormat);
                        }
                    }
                    break;

                case anvilModelField::DATA_TYPE_STRING:
                    $return = stripslashes($value);
                    break;

                case anvilModelField::DATA_TYPE_BOOLEAN:
                case anvilModelField::DATA_TYPE_DECIMAL:
                case anvilModelField::DATA_TYPE_FLOAT:
                case anvilModelField::DATA_TYPE_NUMBER:
                default:
                    $return = $value;
                    break;
            }
        }

        return $return;
    }


    public function hasRows()
    {
        return $this->count() > 0;
    }


    public function getRowArray()
    {
        return $this->_row;
    }


    public function read()
    {
        return true;
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
