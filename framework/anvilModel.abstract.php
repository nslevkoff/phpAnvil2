<?php

//require_once PHPANVIL2_COMPONENT_PATH . 'anvilForm/anvilFormObject.abstract.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';

require_once 'anvilModelFields.class.php';
require_once 'anvilModelField.class.php';

/**
 * @property anvilModelFields $fields
 * @property anvilDataConnectionAbstract $dataConnection
 */
abstract class anvilModelAbstract extends anvilObjectAbstract
{

    public $fields;

//    public $id = 0;

    public $dataConnection;

    public $regional;

    public $primaryTableName;

    //---- Primary Key Column Name in the DB Table
    public $primaryColumnName = 'id';

    //---- Primary Key Field Name in the Model Object
    public $primaryFieldName = 'id';
//    public $primaryTableFieldName = 'id';

//    public $dataFilter;

//    public $idFieldName = 'id';

    protected $_isLoaded = false;

    public $autoLoadAll = false;


    public function __construct($primaryTableName = '', $primaryColumnName = 'id')
    {
        global $phpAnvil;

        parent::__construct();

        $this->enableLog();

        $this->_newFields();

        $this->primaryTableName  = $primaryTableName;
        $this->primaryColumnName = $primaryColumnName;
        $this->dataConnection    = $phpAnvil->db;
        $this->regional          = $phpAnvil->regional;
    }


    public function __get($name)
    {
        $return = null;

        #---- Validate Whether Field Exists
        $isField = $this->fields->exists($name);

        if ($isField) {
            #---- If Value Doesn't Exist, Use Default
            $return = $this->fields->field($name)->value;

            if ($return === '') {
                $return = $this->fields->field($name)->defaultValue;
            }
        } else {
            throw new Exception('Unknown field "' . $name . '"!');
        }

        return $return;
    }


    public function __isset($name)
    {
        $return = $this->fields->exists($name);

        if ($return) {

            $value = $this->fields->field($name)->value;

            //---- Process for empty() PHP function
            if ($value == 0 && !is_null($value)) {
                #---- return false so that empty() works correctly with 0 numbers.
            } else {
                $return = !empty($value);
            }
        } else {
            throw new Exception('Unknown field "' . $name . '"!');
        }

        return $return;
    }


    public function __set($name, $value)
    {
        #---- Validate Whether field Exists
        $isValid = $this->fields->exists($name);

        if ($isValid) {
            #---- Use Custom Function Override if Exists, Otherwise Set Value
            if (method_exists($this, 'set' . $name)) {
                return call_user_func(array($this, 'set' . $name), $value);
            } else {

                if ($this->fields->field($name)->value != $value) {
                    $this->fields->field($name)->priorValue = $this->fields->field($name)->value;
                    $this->fields->field($name)->changed    = true;
                }

                $this->fields->field($name)->value = $value;
            }
        } else {
            //            throw new Exception('Invalid field "' . $name . '"!');

            //---- field is automatically added if a new $name
            $field       = $this->fields->field($name, true);
            $field->name = $name;
            //            $field->defaultValue = $defaultValue;
            $field->value = $value;

            $this->_logDebug($this->fields);
        }

        return true;
    }


    protected function _buildSaveSQL($forceUpdateAll = false)
    {
        $dataFields = '';

        if ($this->isNew()) {
            $sql = 'INSERT INTO ' . $this->primaryTableName . ' (';


            $count = $this->fields->count();
            for ($i = 0; $i < $count; $i++)
            {
                if ($this->fields->field($i)->name != $this->primaryFieldName) {
                    $dataFields .= ', ' . $this->fields->field($i)->fieldName;
                }
            }


            $sql .= substr($dataFields, 2);

            $sql .= ') VALUES (';

            $dataFields = '';


            for ($i = 0; $i < $count; $i++)
            {
                if ($this->fields->field($i)->name != $this->primaryFieldName) {
                    //                    $dataFields .= ', ' . $this->formanvilDataField($i);
                    $dataFields .= ', ' . $this->fields->field($i)->toSave($this->dataConnection);
                }
            }

            $sql .= substr($dataFields, 2);

            $sql .= ')';

        } else {
            $sql = 'UPDATE ' . $this->primaryTableName . ' SET ';

            $count = $this->fields->count();
            for ($i = 0; $i < $count; $i++)
            {
                if ($this->fields->field($i)->name != $this->primaryFieldName && $this->fields->field($i)->fieldType != anvilModelField::DATA_TYPE_ADD_DTS) {
                    if ($forceUpdateAll || (!$forceUpdateAll && $this->fields->field($i)->changed)) {
                        //                        $dataFields .= ', ' . $this->fields->field($i)->fieldName . '=' . $this->formanvilDataField($i);
                        $dataFields .= ', ' . $this->fields->field($i)->fieldName . '=' . $this->fields->field($i)->toSave($this->dataConnection);
                    }
                }
            }

            $sql .= substr($dataFields, 2);

            //			$sql .= ' WHERE ' . $this->_dataFields['id'] . '=' . $this->id;
            $sql .= ' WHERE ' . $this->primaryColumnName . '=' . intval($this->fields->field($this->primaryFieldName)->value);

            //            if (!empty($this->dataFilter)) {
            //                $sql .= ' AND ' . $this->dataFilter;
            //            }
        }

        return $sql;
    }


    protected function _newFields()
    {
        $this->fields = new anvilModelFields($this);
    }


    public function delete($sql = '')
    {
        $primaryValue = $this->fields->field($this->primaryFieldName)->value;
        $return       = !empty($primaryValue);

        if ($return) {
            #---- Build SQL if Empty
            if (empty($sql)) {
                $sql = 'DELETE';
                $sql .= ' FROM ' . $this->dataFrom;
                $sql .= ' WHERE ' . $this->primaryColumnName . '=' . $primaryValue;

                //            if (!empty($this->dataFilter)) {
                //                $sql .= ' AND ' . $this->dataFilter;
                //            }

            }

            $this->dataConnection->execute($sql);

            $this->resetFields();
            $this->_isLoaded = false;
        }


        return $return;
    }


    public function isField($name)
    {
        return $this->fields->exists($name);
    }


    public function isLoaded()
    {
        return $this->_isLoaded;
    }


    public function isNew()
    {
        return intval($this->fields->field($this->primaryFieldName)->value) === 0;
    }


    public function load($sql = '')
    {
        $return     = false;
        $dataFields = '';

        #---- Build SQL if Empty
        if (empty($sql)) {
            //            $this->_logDebug($this->primaryFieldName, '$this->primaryFieldName');
            //            $this->_logDebug($this->fields, '$this->fields');


            $primaryValue = $this->fields->field($this->primaryFieldName)->value;


            $sql = 'SELECT ';

            if ($this->autoLoadAll) {
                $sql .= '*';
            } else {
                $count = $this->fields->count();

                for ($i = 0; $i < $count; $i++)
                {
                    $dataFields .= ', ' . $this->fields->field($i)->fieldName;
                }

                $sql .= substr($dataFields, 2);
            }

            $sql .= ' FROM ' . $this->primaryTableName;
            $sql .= ' WHERE ' . $this->primaryColumnName . '=' . intval($primaryValue);

            //            if (!empty($this->dataFilter)) {
            //                $sql .= ' AND ' . $this->dataFilter;
            //            }
        }

        //        $this->_logDebug($sql, '$sql');

        $objRS = $this->dataConnection->execute($sql);

        if ($objRS->read()) {


            $count = $this->fields->count();

            for ($i = 0; $i < $count; $i++)
            {

                $this->fields->field($i)->value = $objRS->data($this->fields->field($i)->fieldName);
                //                $this->fields->field($i)->value = $this->formanvilDisplayField($this->fields->field($i)->name);

            }


            $return = true;
        }
        $objRS->close();

        $this->_isLoaded = $return;

        return $return;
    }


    public function resetChanged()
    {
        $this->fields->resetChanged();
    }


    public function resetFields()
    {
        $this->fields->reset();
    }


    public function save($sql = '', $id_sql = '')
    {
        $return = false;

        #---- Build SQL if Empty
        if (empty($sql)) {
            $sql = $this->_buildSaveSQL();
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
                $this->fields->field($this->primaryFieldName)->value = $objRS->data('id');
            }
        }

        return $return;
    }


    public function setFieldValues(array $fieldValues = null)
    {

        if (is_array($fieldValues)) {
            foreach ($fieldValues as $name => $newValue)
            {
                if ($this->fields->exists($name)) {
                    if ($this->fields->field($name)->value != $newValue) {
                        $this->fields->field($name)->priorValue = $this->fields->field($name)->value;
                        $this->fields->field($name)->changed    = true;
                    }

                    $this->fields->field($name)->value = $newValue;
                }
            }
        }
    }


    public function toArray()
    {
        return $this->fields->toArray();
    }
}


?>