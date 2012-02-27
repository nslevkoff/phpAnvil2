<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';

require_once 'anvilModelField.class.php';


class anvilModelFields extends anvilObjectAbstract
{

    protected $_fields = array();
    protected $_fieldIndex = array();

    public $model;


    public function __construct($model)
    {
        $this->model = $model;
    }

    public function exists($fieldName)
    {
        if (is_numeric($fieldName))
        {
            $fieldName = $this->_fieldIndex[$fieldName];
        }

        $return = isset($this->_fields[$fieldName]);

        return $return;
    }


    protected function _newField($fieldName = '')
    {
        return new anvilModelField($this->model, $fieldName);
    }


    /**
     * @param string $fieldName
     * @param bool $addIfNotExist
     * @return anvilModelField
     */
    public function field($fieldName, $addIfNotExist = false)
    {
        $return = false;

//        $this->_logDebug($fieldName, '$fieldName');

        if (is_numeric($fieldName))
        {
            $fieldName = $this->_fieldIndex[$fieldName];
        }

        if (array_key_exists($fieldName, $this->_fields))
        {
            $return = $this->_fields[$fieldName];
        } else {
            if ($addIfNotExist)
            {
                $this->_fields[$fieldName] = $this->_newfield($fieldName);
                $this->_fieldIndex[] = $fieldName;

                $return = $this->_fields[$fieldName];
            }
        }

        return $return;
    }


    public function &__get($fieldName)
    {

        if (!array_key_exists($fieldName, $this->_fields))
        {
            $this->_fields[$fieldName] = $this->_newfield($fieldName);
            $this->_fieldIndex[] = $fieldName;
        }

//        if (isset($this->_fields[$fieldName]))
//        {
            $return = $this->_fields[$fieldName];
//        }

        return $return;
    }


    public function __isset($fieldName)
    {
        $return = isset($this->_fields[$fieldName]);

        return $return;

    }


    public function isChanged()
    {
        $count = $this->count();

        for ($i = 0; $i < $count; $i++)
        {
            if ($this->_fields[$this->_fieldIndex[$i]]->changed) {
                return true;
            };
        }

        return false;
    }


    public function reset()
    {
        $count = $this->count();
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->_fields[$this->_fieldIndex[$i]]->defaultValue)) {
                $this->_fields[$this->_fieldIndex[$i]]->value = $this->_fields[$this->_fieldIndex[$i]]->defaultValue;
            } else {
                $this->_fields[$this->_fieldIndex[$i]]->value = '';
            }
            $this->_fields[$this->_fieldIndex[$i]]->changed = false;
        }
    }


    public function resetChanged()
    {
        $count = $this->count();

        for ($i = 0; $i < $count; $i++)
        {
            $this->_fields[$this->_fieldIndex[$i]]->changed = false;
        }
    }


    public function toArray()
    {
        $newArray = array();

//        $count = $this->count();

        foreach($this->_fields as $name => $object)
        {
            $newArray[$object->name] = $object->value;
        }

        return $newArray;
    }


    public function count()
    {
        return count($this->_fields);
    }

}

?>
