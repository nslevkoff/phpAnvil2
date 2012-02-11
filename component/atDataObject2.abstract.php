<?php
/**
 * @file
 * @author         Nick Slevkoff <nick@slevkoff.com>
 * @copyright      Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        phpAnvilTools atData
 */


require_once('atDynamicObject2.abstract.php');


/**
 * Base Dynamic Data Object Class
 *
 * This class adds database support to the dynamic class.
 *
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools atData
 */
abstract class atDataObjectAbstract2 extends atDynamicObjectAbstract2
{
    const VERSION = '2.0';
    const BUILD   = '6';


    const DATA_TYPE_IGNORE  = 0;
    const DATA_TYPE_BOOLEAN = 1;
    const DATA_TYPE_DATE    = 2;
    const DATA_TYPE_DTS     = 3;

    const DATA_TYPE_NUMBER  = 4;
    const DATA_TYPE_NUMERIC = self::DATA_TYPE_NUMBER;
    const DATA_TYPE_INTEGER = self::DATA_TYPE_NUMBER;

    const DATA_TYPE_STRING  = 5;
    const DATA_TYPE_ADD_DTS = 6;

    const DATA_TYPE_FLOAT   = 7;
    const DATA_TYPE_DECIMAL = self::DATA_TYPE_FLOAT;

    const DATA_TYPE_TIME       = 8;
    const DATA_TYPE_EMAIL      = 9;
    const DATA_TYPE_PHONE      = 10;
    const DATA_TYPE_CREDITCARD = 11;
    const DATA_TYPE_SSN        = 12;
    const DATA_TYPE_ARRAY      = 13;


    public $atDataConnection;
    public $atRegional;
    public $atDictionary;


    public $dataFrom;
    public $dataFilter;

    public $dateDefault = null;
    public $dtsDefault = null;

    public $idPropertyName = 'id';

    protected $_isLoaded = false;


    public function __construct(
        $atDataConnection,
        $atRegional,
        $atDictionary,
        $dataFrom = '',
//        $id = 0,
        $dataFilter = '')
    {
        parent::__construct();

        //        $this->enableLog();


        $this->atDataConnection = $atDataConnection;
        $this->atRegional       = $atRegional;
        $this->atDictionary     = $atDictionary;

        $this->dataFrom = $dataFrom;
        //$this->_values['id'] = $id;
        $this->dataFilter = $dataFilter;

        //        $this->id = $id;
    }


    public function __get($name)
    {
        $return = null;

        $return = parent::__get($name);

        if ($name == $this->idPropertyName) {
            $return = intval($return);
        }

        return $return;
    }


    protected function newProperties()
    {
        if (!isset($this->properties)) {
            $this->properties = new atDataObjectProperties();
        }
    }


    protected function addProperty(
        $name,
        $tableName = '',
        $fieldName = '',
        $fieldType = '',
        $defaultValue = null,
        $maxLength = 40,
        $allowNull = true,
        $readOnly = false)
    {
        $property = parent::addProperty($name, $defaultValue);
        //        $property = parent::addProperty($name);

        $property->tableName = $tableName;
        $property->fieldName = $fieldName;
        $property->fieldType = $fieldType;
        $property->maxLength = $maxLength;
        $property->allowNull = $allowNull;
        $property->readOnly  = $readOnly;

        if (is_null($defaultValue) && !$allowNull) {
            switch ($fieldType)
            {
                case self::DATA_TYPE_ADD_DTS:
                case self::DATA_TYPE_DTS:
                case self::DATA_TYPE_DATE:
//                    $property->defaultValue = '0000-00-00 00:00:00';
                    $property->defaultValue = null;
                    break;

                case self::DATA_TYPE_BOOLEAN:
                    $property->defaultValue = false;
                    break;

                case self::DATA_TYPE_DECIMAL:
                case self::DATA_TYPE_NUMBER:
//                    if ($property->allowNull) {
//                        $property->defaultValue = null;
//                    } else {
                    $property->defaultValue = 0;
//                    }
                    break;

                case self::DATA_TYPE_STRING:
                default:
                    $property->defaultValue = null;
                    break;
            }
        }

        //        $property->value = $property->defaultValue;

        return $property;
    }


    public function count()
    {
        $return = 0;

        $idProperty = $this->properties->property($this->idPropertyName);

        $sql = 'SELECT count(' . $idProperty->fieldName . ') AS total_rows FROM ' . $this->dataFrom;

        if (!empty($this->dataFilter)) {
            $sql .= ' WHERE ' . $this->dataFilter;
        }

        $objRS = $this->atDataConnection->execute($sql);
        if ($objRS->read()) {
            $return = $objRS->data('total_rows');
        }
        $objRS->close();

        return $return;
    }


    public function delete($sql = '')
    {
        $return = true;

        $idProperty = $this->properties->property($this->idPropertyName);

        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = 'DELETE';
            $sql .= ' FROM ' . $this->dataFrom;
            $sql .= ' WHERE ' . $idProperty->fieldName . '=' . $idProperty->value;

            if (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }

        }

        $this->atDataConnection->execute($sql);

        $this->resetProperties();
        $this->_isLoaded = false;

        return $return;
    }


    public function formatDisplayField($propertyName, $format1 = '', $format2 = '')
    {
        $return = '';

        $isValid = $this->properties->exists($propertyName);

        if ($isValid) {
            $property = $this->properties->property($propertyName);

            //		    if (!array_key_exists($propertyName, $this->_values))
            //            {
            //			    $this->_values[$propertyName] = $this->_properties[$propertyName];
            //		    }

            switch ($property->fieldType)
            {
                case self::DATA_TYPE_BOOLEAN:
//				    if ($property->value)
//                    {
//					    if (!empty($format1))
//                        {
//						    $return = $format1;
//					    } else {
//						    $return = $this->booleanFormatTrue;
//					    }
//				    } else {
//					    if (!empty($format2))
//                        {
//						    $return = $format2;
//					    } else {
//						    $return = $this->booleanFormatFalse;
//					    }
//				    }

                    $return = $property->value;
                    break;

                case self::DATA_TYPE_DATE:
                    if ($property->value == '0000-00-00 00:00:00') {
                        $property->value = '';
                    }

                    if (empty($property->value) || strtolower($property->value) == 'null') {
                        //					    $return = $this->dateDefault;
                    } else {
                        if (isset($this->atRegional->dateTimeZone)) {
                            $value = new DateTime($property->value, $this->atRegional->dateTimeZone);

                            $return = $value->format($this->atRegional->dateFormat);

                            //					    if (!empty($format1))
                            //                        {
                            //						    $return = strftime($format1, strtotime($property->value));
                            //					    } else {
                            //						    $return = strftime($this->dateFormat, strtotime($property->value));
                            //					    }
                        } else {
                            $value  = new DateTime($property->value, new DateTimeZone('PST'));
                            $return = $value->format($this->atRegional->dateFormat);
                        }
                    }
                    break;

                case self::DATA_TYPE_DTS:
                case self::DATA_TYPE_ADD_DTS:
//                    FB::log($property->name, '$property->name');
//                    FB::log($property->value, '$property->value');

//                    echo "\n atRegional = " . print_r($this->atRegional) . "\n";

                    if ($property->value == '0000-00-00 00:00:00') {
                        $property->value = '';
                    }

                    if (empty($property->value) || strtolower($property->value) == 'null') {
                        //					    $return = $this->dtsDefault;
                    } else {
                        if (isset($this->atRegional->dateTimeZone)) {

                            $value = new DateTime($property->value, $this->atRegional->dateTimeZone);

                            $return = $value->format($this->atRegional->dtsFormat);

                            //					    if (!empty($format1))
                            //                        {
                            //						    $return = strftime($format1, strtotime($property->value));
                            //					    } else {
                            //						    $return = strftime($this->dtsFormat, strtotime($property->value));
                            //					    }
                        } else {
                            $value  = new DateTime($property->value, new DateTimeZone('PST'));
                            $return = $value->format($this->atRegional->dtsFormat);
                        }
                    }
                    break;

                case self::DATA_TYPE_STRING:
                    $return = stripslashes($property->value);
                    break;

                case self::DATA_TYPE_DECIMAL:
                case self::DATA_TYPE_FLOAT:
                case self::DATA_TYPE_NUMBER:
                default:
                    $return = $property->value;
                    break;
            }
        } else {
            throw new Exception('Invalid property "' . $propertyName . '"!');
        }

        //        FB::log($return, $property->name);

        return $return;
    }


    public function isNew()
    {
        //        $this->logDebug($this->properties->property($this->idPropertyName), 'ID Property');

        return intval($this->properties->property($this->idPropertyName)->value) === 0;
    }


    public function load($sql = '')
    {
        $return     = false;
        $dataFields = '';

        //if (!$this->isNew()) {
        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = 'SELECT ';

            $count = $this->properties->count();
            for ($i = 0; $i < $count; $i++)
            {
                $dataFields .= ', ' . $this->properties->property($i)->fieldName;
            }

            $sql .= substr($dataFields, 2);

            $sql .= ' FROM ' . $this->dataFrom;
            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '=' . intval($this->properties->property($this->idPropertyName)->value);

            if (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }

        }

        $objRS = $this->atDataConnection->execute($sql);

        if ($objRS->read()) {

            //                echo '.. SQL Executed and read, now populating the properties...' . "\n";

            $count = $this->properties->count();

            //                echo '.. $count = ' . $count . ' ..' . "\n";

            for ($i = 0; $i < $count; $i++)
            {
                //                    echo '.' . $i;
                //                    echo ':' . $this->properties->property($i)->fieldName;
                //                    echo ':' . $this->properties->property($i)->name;
                //                    echo ':' . $this->formatDisplayField($this->properties->property($i)->name);

                $this->properties->property($i)->value = $objRS->data($this->properties->property($i)->fieldName);
                $this->properties->property($i)->value = $this->formatDisplayField($this->properties->property($i)->name);

            }
            //                echo '.done.' . "\n";


            $return = true;
        }
        $objRS->close();


        //}
        $this->_isLoaded = $return;

        return $return;
    }


    public function formatDataField($propertyName)
    {
        $return = '';

        $isValid = $this->properties->exists($propertyName);

        if ($isValid) {
            $property = $this->properties->property($propertyName);

            switch ($property->fieldType)
            {
                case self::DATA_TYPE_BOOLEAN:
                    $return = $this->atDataConnection->dbBoolean($property->value);
                    break;
                case self::DATA_TYPE_DATE:

                    $value = isset($property->value)
                            ? $property->value
                            : ($property->allowNull
                                    ? null
                                    : (isset($property->defaultValue)
                                            ? $property->defaultValue
                                            : $this->dateDefault));

                    $return = $value;

                    if (!is_null($value)) {
                        $value = new DateTime($value, new DateTimeZone('UTC'));

                        $return = $value->format($this->atDataConnection->dateFormat);
                        $return = $this->atDataConnection->dbDate($return);
                    }

                    break;
                case self::DATA_TYPE_DTS:

                    $value = !empty($property->value)
                            ? $property->value
                            : ($property->allowNull
                                    ? null
                                    : (isset($property->defaultValue)
                                            ? $property->defaultValue
                                            : $this->dtsDefault));

                    $return = $value;

                    if (!is_null($value)) {
                        $value = new DateTime($value, new DateTimeZone('UTC'));

                        $return = $value->format($this->atDataConnection->dtsFormat);
                        $return = $this->atDataConnection->dbDTS($return);
                    }

                    break;
                case self::DATA_TYPE_STRING:

                    $return = isset($property->value)
                            ? $this->atDataConnection->dbString($property->value)
                            :
                            ($property->allowNull
                                    ? null
                                    :
                                    (isset($property->defaultValue)
                                            ? $this->atDataConnection->dbString($property->defaultValue)
                                            : $this->atDataConnection->dbString('')));

                    if (empty($return)) {
                        $return = null;
                    }

                    break;

                case self::DATA_TYPE_ADD_DTS:
                    $return = 'NOW()';
                    break;

                case self::DATA_TYPE_DECIMAL:
                case self::DATA_TYPE_FLOAT:
                    $return = isset($property->value)
                            ? floatval($property->value)
                            :
                            ($property->allowNull
                                    ? null
                                    : (isset($property->defaultValue)
                                            ? $property->defaultValue
                                            : 0));
                    break;

                case self::DATA_TYPE_NUMBER:
                default:

                    $return = isset($property->value)
                            ? intval($property->value)
                            : ($property->allowNull
                                    ? null
                                    : (isset($property->defaultValue)
                                            ? $property->defaultValue
                                            : 0));
                    break;
            }
        } else {
            throw new Exception('Invalid property "' . $propertyName . '"!');
        }


        if (is_null($return)) {
            $return = 'null';
        }

        return $return;
    }


    public function buildSaveSQL()
    {
        $dataFields = '';

        if ($this->isNew()) {
            $sql = 'INSERT INTO ' . $this->dataFrom . ' (';


            $count = $this->properties->count();
            for ($i = 0; $i < $count; $i++)
            {
                if ($this->properties->property($i)->name != $this->idPropertyName) {
                    $dataFields .= ', ' . $this->properties->property($i)->fieldName;
                }
            }


            $sql .= substr($dataFields, 2);

            $sql .= ') VALUES (';

            $dataFields = '';


            for ($i = 0; $i < $count; $i++)
            {
                if ($this->properties->property($i)->name != $this->idPropertyName) {
                    $dataFields .= ', ' . $this->formatDataField($i);
                }
            }

            $sql .= substr($dataFields, 2);

            $sql .= ')';

        } else {
            $sql = 'UPDATE ' . $this->dataFrom . ' SET ';

            $count = $this->properties->count();
            for ($i = 0; $i < $count; $i++)
            {
                if ($this->properties->property($i)->name != $this->idPropertyName && $this->properties->property($i)->fieldType != self::DATA_TYPE_ADD_DTS) {
                    $dataFields .= ', ' . $this->properties->property($i)->fieldName . '=' . $this->formatDataField($i);
                }
            }

            $sql .= substr($dataFields, 2);

            //			$sql .= ' WHERE ' . $this->_dataFields['id'] . '=' . $this->id;
            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '=' . intval($this->properties->property($this->idPropertyName)->value);

            if (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }
        }

        return $sql;
    }


    public function save($sql = '', $id_sql = '')
    {
        $return = false;

        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = $this->buildSaveSQL();
        }

        //        echo '.. Save SQL = ' . $sql . ' ..' . "\n";

        $this->logVerbose($sql, 'Save SQL');

        $return = $this->atDataConnection->execute($sql);

        if ($this->isNew()) {
            if (empty($id_sql)) {
                //				$id_sql = 'SELECT LAST_INSERT_ID() AS id FROM ' . $this->dataFrom;
                $id_sql = 'SELECT LAST_INSERT_ID() AS id';
            }

            $objRS = $this->atDataConnection->execute($id_sql);
            if ($objRS->read()) {
                $this->properties->property($this->idPropertyName)->value = $objRS->data('id');
            }
        }

        return $return;
    }


    public function detectNextID($customFilter = '')
    {
        $return = false;

        if (!$this->isNew()) {
            $sql = 'SELECT MIN(' . $this->properties->property($this->idPropertyName)->fieldName . ') AS next_id FROM ' . $this->dataFrom;
            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '>' . $this->properties->property($this->idPropertyName)->value;

            if (!empty($customFilter)) {
                $sql .= ' AND ' . $customFilter;
            } elseif (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }

            $objRS = $this->atDataConnection->execute($sql);
            if ($objRS->read()) {
                $return = $objRS->data('next_id');
            }
            $objRS->close();
        }
        return $return;
    }


    public function detectPreviousID($customFilter = '')
    {
        $return = false;

        if (!$this->isNew()) {
            $sql = 'SELECT MAX(' . $this->properties->property($this->idPropertyName)->fieldName . ') AS previous_id FROM ' . $this->dataFrom;
            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '<' . $this->properties->property($this->idPropertyName)->value;

            if (!empty($customFilter)) {
                $sql .= ' AND ' . $customFilter;
            } elseif (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }

            $objRS = $this->atDataConnection->execute($sql);
            if ($objRS->read()) {
                $return = $objRS->data('previous_id');
            }
            $objRS->close();
        }
        return $return;
    }


    public function isLoaded()
    {
        return $this->_isLoaded;
    }

}


class atDataObjectProperties extends atDynamicObjectProperties
{
    protected function newProperty($propertyName = '')
    {
        return new atDataObjectProperty($propertyName);
    }
}


class atDataObjectProperty extends atDynamicObjectProperty
{
    public $tableName;
    public $fieldName;
    public $fieldType;
    public $maxLength;
    public $decimalPlace;
    public $allowNull = true;
    public $readOnly = false;
    public $dataRegEx;
    public $displayRegEx;
}


?>