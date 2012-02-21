<?php
require_once 'anvilObject.abstract.php';

require_once 'anvilDynamicProperty.class.php';

class anvilDynamicProperties extends anvilObjectAbstract
{

    protected $_properties = array();
    protected $_propertyIndex = array();


    public function exists($propertyName)
    {
        $return = false;
//        $propertyName = strtolower($propertyName);

        if (is_numeric($propertyName))
        {
            $propertyName = $this->_propertyIndex[$propertyName];
        } else {
//            $propertyName = strtolower($propertyName);
        }

        $return = isset($this->_properties[$propertyName]);

        return $return;
    }


    protected function _newProperty($propertyName = '')
    {
        return new anvilDynamicProperty($propertyName);
    }


    public function property($propertyName, $addIfNotExist = false)
    {
        $return = false;

//        $this->_logDebug($propertyName, '$propertyName');

        if (is_numeric($propertyName))
        {
            $propertyName = $this->_propertyIndex[$propertyName];
        } else {
            $propertyName = strtolower($propertyName);
        }

        if (array_key_exists($propertyName, $this->_properties))
        {
            $return = $this->_properties[$propertyName];
        } else {
            if ($addIfNotExist)
            {
                $this->_properties[$propertyName] = $this->_newProperty($propertyName);
                $this->_propertyIndex[] = $propertyName;

                $return = $this->_properties[$propertyName];
            }
        }

        return $return;
    }


    public function &__get($propertyName)
    {

//        $return = null;
//        $propertyName = strtolower($propertyName);

        if (!array_key_exists($propertyName, $this->_properties))
        {
            $this->_properties[$propertyName] = $this->_newProperty($propertyName);
            $this->_propertyIndex[] = $propertyName;
        }

//        if (isset($this->_properties[$propertyName]))
//        {
            $return = $this->_properties[$propertyName];
//        }

        return $return;
    }


    public function __isset($propertyName)
    {
//        $propertyName = strtolower($propertyName);

//        $return = array_key_exists($propertyName, $this->_properties);
        $return = isset($this->_properties[$propertyName]);

        return $return;

    }


//    public function __set($propertyName, $value)
//    {
//        $propertyName = strtolower($propertyName);
//    }


    public function reset()
    {
        $count = $this->count();
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->_properties[$this->_propertyIndex[$i]]->defaultValue)) {
                $this->_properties[$this->_propertyIndex[$i]]->value = $this->_properties[$this->_propertyIndex[$i]]->defaultValue;
            } else {
                $this->_properties[$this->_propertyIndex[$i]]->value = '';
            }
            $this->_properties[$this->_propertyIndex[$i]]->changed = false;
        }
    }


    public function resetChanged()
    {
        $count = $this->count();

//        fb::log($count, '$count');
//        fb::log($this->_properties, '$this->_properties');
        
//        $propertyKeys = array_keys($this->_properties);

        for ($i = 0; $i < $count; $i++)
        {
//            $this->_properties[$propertyKeys[$i]]->changed = false;
            $this->_properties[$this->_propertyIndex[$i]]->changed = false;
        }
    }


    public function toArray()
    {
        $newArray = array();

        $count = $this->count();

//        fb::log($count, '$count');
//        fb::log($this->_properties, '$this->_properties');

//        for ($i = 0; $i < $count; $i++)
//        {
//            $newArray[$this->_properties[$i]->name] = $this->_properties[$i]->value;
//        }

        foreach($this->_properties as $name => $object)
        {
//            $newArray[$name] = $object->value;
            $newArray[$object->name] = $object->value;
        }

//        fb::log($newArray, '$newArray');

        return $newArray;
    }


    public function count()
    {
        return count($this->_properties);
    }

}

?>
