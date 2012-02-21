<?php
/**
 * @file
 * @author         Nick Slevkoff <nick@slevkoff.com>
 * @copyright      Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        phpAnvilTools anvilData
 */


require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';


/**
 * Base Dynamic Data Object Class
 *
 * This class adds database support to the dynamic class.
 *
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilData
 */
abstract class anvilDataObjectAbstract extends anvilDynamicObjectAbstract
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


    public $id = 0;

    public $dataConnection;
    public $regional;
    public $dictionary;


    public $dataFrom;
    public $dataFilter;

    public $dateDefault = null;
    public $dtsDefault = null;

    public $idPropertyName = 'id';

    protected $_isLoaded = false;

    public $autoLoadAll = false;


    public function __construct(
//        $dataConnection,
//        $regional,
//        $dictionary,
        $dataFrom = '',
//        $id = 0,
        $dataFilter = '')
    {
        global $phpAnvil;

        parent::__construct();

//        $this->enableLog();


        $this->dataConnection = $phpAnvil->db;
        $this->regional       = $phpAnvil->regional;
        //        $this->dictionary     = $dictionary;

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


    protected function _newProperties()
    {
        if (!isset($this->properties)) {
            $this->properties = new anvilDataObjectProperties();
        }
    }


/*
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
*/

    public function count()
    {
        $return = 0;

        $idProperty = $this->properties->property($this->idPropertyName);

        $sql = 'SELECT count(' . $idProperty->fieldName . ') AS total_rows FROM ' . $this->dataFrom;

        if (!empty($this->dataFilter)) {
            $sql .= ' WHERE ' . $this->dataFilter;
        }

        $objRS = $this->dataConnection->execute($sql);
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

        $this->dataConnection->execute($sql);

        $this->resetProperties();
        $this->_isLoaded = false;

        return $return;
    }


    public function formanvilDisplayField($propertyName, $format1 = '', $format2 = '')
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
//						    $return = $this->booleanFormanvilTrue;
//					    }
//				    } else {
//					    if (!empty($format2))
//                        {
//						    $return = $format2;
//					    } else {
//						    $return = $this->booleanFormanvilFalse;
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
                        if (isset($this->regional->dateTimeZone)) {
                            $value = new DateTime($property->value, $this->regional->dateTimeZone);

                            $return = $value->format($this->regional->dateFormat);

                            //					    if (!empty($format1))
                            //                        {
                            //						    $return = strftime($format1, strtotime($property->value));
                            //					    } else {
                            //						    $return = strftime($this->dateFormat, strtotime($property->value));
                            //					    }
                        } else {
                            $value  = new DateTime($property->value, new DateTimeZone('PST'));
                            $return = $value->format($this->regional->dateFormat);
                        }
                    }
                    break;

                case self::DATA_TYPE_DTS:
                case self::DATA_TYPE_ADD_DTS:
//                    FB::log($property->name, '$property->name');
//                    FB::log($property->value, '$property->value');

//                    echo "\n regional = " . print_r($this->regional) . "\n";

                    if ($property->value == '0000-00-00 00:00:00') {
                        $property->value = '';
                    }

                    if (empty($property->value) || strtolower($property->value) == 'null') {
                        //					    $return = $this->dtsDefault;
                    } else {
                        if (isset($this->regional->dateTimeZone)) {

                            $value = new DateTime($property->value, $this->regional->dateTimeZone);

                            $return = $value->format($this->regional->dtsFormat);

                            //					    if (!empty($format1))
                            //                        {
                            //						    $return = strftime($format1, strtotime($property->value));
                            //					    } else {
                            //						    $return = strftime($this->dtsFormat, strtotime($property->value));
                            //					    }
                        } else {
                            $value  = new DateTime($property->value, new DateTimeZone('PST'));
                            $return = $value->format($this->regional->dtsFormat);
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
        //        $this->_logDebug($this->properties->property($this->idPropertyName), 'ID Property');

//        return intval($this->properties->property($this->idPropertyName)->value) === 0;
        $idPropertyName = $this->idPropertyName;

        return intval($this->$idPropertyName) === 0;
    }


    public function load($sql = '')
    {
        $return     = false;
        $dataFields = '';

//        $this->_logDebug($this->idPropertyName, '$this->idPropertyName');
//        $this->_logDebug($this->properties->property($this->idPropertyName)->fieldName, 'ID.fieldName');

        //if (!$this->isNew()) {
        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = 'SELECT ';

            if ($this->autoLoadAll) {
                $sql .= '*';
            } else {
                $count = $this->properties->count();

                //            $this->_logDebug($this->properties, '$this->properties');
                //            $this->_logDebug($count, '$count');

                for ($i = 0; $i < $count; $i++)
                {
                    $dataFields .= ', ' . $this->properties->property($i)->fieldName;
                }

                $sql .= substr($dataFields, 2);
            }

            $sql .= ' FROM ' . $this->dataFrom;
//            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '=' . intval($this->properties->property($this->idPropertyName)->value);
            $idPropertyName = $this->idPropertyName;
            $sql .= ' WHERE ' . $this->properties->property($this->idPropertyName)->fieldName . '=' . intval($this->$idPropertyName);

            if (!empty($this->dataFilter)) {
                $sql .= ' AND ' . $this->dataFilter;
            }
        }

//        $this->_logDebug($sql, '$sql');

        $objRS = $this->dataConnection->execute($sql);

        if ($objRS->read()) {


            //                echo '.. SQL Executed and read, now populating the properties...' . "\n";
/*
            if ($this->autoLoadAll) {
                //                $this->_logDebug($objRS, '$objRS');

                for ($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext())
                {
                    $objColumn = $objRS->columns->current();

//                    $this->_logDebug($objColumn->name, 'Column');

//                    $this->_logDebug($objColumn, '$objColumn');

//                    $idName = $this->idPropertyName;
//                    $fieldName = $objColumn->name;

                    $objProperty = false;
                    $objProperty = $this->properties->findFieldName($objColumn->name);

//                    $this->_logDebug($objProperty , '$objProperty');

//                    if ($objColumn->name == $this->properties->property($idName)->fieldName) {
//                        $this->$idName = $objRS->data($objColumn->name);
                    if ($objProperty) {
                        $propertyName = $objProperty->name;
                        $this->$propertyName = $objRS->data($objColumn->name);
                    } else {
//                        $propertyName = $this->_toCamelCase($objColumn->name);
//                        $this->_logDebug($propertyName, '$propertyName');

//                        $this->$propertyName = $objRS->data($objColumn->name);

                        $this->properties->$propertyName->fieldName = $objColumn->name;

                        switch ($objColumn->type) {
                            case 'int':
                                $this->properties->$propertyName->fieldType = self::DATA_TYPE_INTEGER;
                                break;
                            case 'string':
                                $this->properties->$propertyName->fieldType = self::DATA_TYPE_STRING;
                                break;
                            case 'datetime':
                                $this->properties->$propertyName->fieldType = self::DATA_TYPE_DTS;
                                break;
                        }


                    }

                }

            } else {
*/
                $count = $this->properties->count();

                //                echo '.. $count = ' . $count . ' ..' . "\n";

                for ($i = 0; $i < $count; $i++)
                {
                    //                    echo '.' . $i;
                    //                    echo ':' . $this->properties->property($i)->fieldName;
                    //                    echo ':' . $this->properties->property($i)->name;
                    //                    echo ':' . $this->formanvilDisplayField($this->properties->property($i)->name);

//                    $this->properties->property($i)->value = $objRS->data($this->properties->property($i)->fieldName);
//                    $this->properties->property($i)->value = $this->formanvilDisplayField($this->properties->property($i)->name);

                    $propertyName = $this->properties->property($i)->name;
                    $this->$propertyName = $objRS->data($this->properties->property($i)->fieldName);
//                    $this->$propertyName = $this->formanvilDisplayField($propertyName);

                }
//            }

//            $this->_logDebug($this);

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
                    $return = $this->dataConnection->dbBoolean($property->value);
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

                        $return = $value->format($this->dataConnection->dateFormat);
                        $return = $this->dataConnection->dbDate($return);
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

                        $return = $value->format($this->dataConnection->dtsFormat);
                        $return = $this->dataConnection->dbDTS($return);
                    }

                    break;
                case self::DATA_TYPE_STRING:

                    $return = isset($property->value)
                            ? $this->dataConnection->dbString($property->value)
                            :
                            ($property->allowNull
                                    ? null
                                    :
                                    (isset($property->defaultValue)
                                            ? $this->dataConnection->dbString($property->defaultValue)
                                            : $this->dataConnection->dbString('')));

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
                    $dataFields .= ', ' . $this->formanvilDataField($i);
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
                    $dataFields .= ', ' . $this->properties->property($i)->fieldName . '=' . $this->formanvilDataField($i);
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

        $this->_logVerbose($sql, 'Save SQL');

        $return = $this->dataConnection->execute($sql);

        if ($this->isNew()) {
            if (empty($id_sql)) {
                //				$id_sql = 'SELECT LAST_INSERT_ID() AS id FROM ' . $this->dataFrom;
                $id_sql = 'SELECT LAST_INSERT_ID() AS id';
            }

            $objRS = $this->dataConnection->execute($id_sql);
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

            $objRS = $this->dataConnection->execute($sql);
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

            $objRS = $this->dataConnection->execute($sql);
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


    private function _fromCamelCase($str)
    {
        $str[0] = strtolower($str[0]);
        return preg_replace('/([A-Z])/e', "'_' . strtolower('\\1')", $str);
    }


    private function _toCamelCase($str, $capitaliseFirstChar = false)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }

        return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $str);
    }

}


class anvilDataObjectProperties extends anvilDynamicProperties
{

    protected $_fieldNames = array();

    protected function _newProperty($propertyName = '')
    {
        return new anvilDataObjectProperty($propertyName);
    }

    public function findFieldName($fieldName)
    {

        if (!count($this->_fieldNames)) {
            $count = $this->count();

            for ($i = 0; $i < $count; $i++)
            {
                $this->_fieldNames[$this->_propertyIndex[$i]] = $this->_properties[$this->_propertyIndex[$i]]->fieldName;
            }
        }

//        $this->_logDebug($fieldName, '$fieldName');
//        $this->_logDebug($this->_fieldNames, '$this->_fieldNames');

        $return = array_search($fieldName, $this->_fieldNames);

//        $this->_logDebug($return, '$return');

        if ($return === FALSE) {

/*
            $this->_logDebug('Searching through properties...');

            $count = $this->count();

            $this->_logDebug($count, '$count');

            for ($i = 0; ($i < $count) && !$return; $i++)
            {
                $this->_logDebug($i, '$i');

//                $this->_logDebug($this->_propertyIndex[$i], '$this->_propertyIndex[$i]');

//                echo '$this->_propertyIndex[$i] = ' . $this->_propertyIndex[$i] . '<br />';

                if ($this->_properties[$this->_propertyIndex[$i]]->fieldName == $fieldName) {
                    $return = $this->_properties[$this->_propertyIndex[$i]];

                    $this->_fieldNames[$this->_propertyIndex[$i]] = $fieldName;
                }

            }
*/
        } else {
            $return = $this->_properties[$return];
        }

        return $return;
    }
}


class anvilDataObjectProperty extends anvilDynamicProperty
{
    public $tableName;
    public $fieldName;
    public $fieldType = anvilDataObjectAbstract::DATA_TYPE_STRING;
    public $maxLength;
    public $decimalPlace;
    public $allowNull = true;
    public $readOnly = false;
    public $dataRegEx;
    public $displayRegEx;
}


?>