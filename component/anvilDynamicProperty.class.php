<?php

class anvilDynamicProperty
{
    public $name;
    public $defaultValue = null;
    protected $_value;
    public $changed = false;
    public $priorValue;


    public function __construct($name = '')
    {
        $this->name = $name;
    }

    public function __get($name)
    {
        $return = null;

        if ($name == 'value') {
            $return = $this->_value;
        }

        return $return;
    }

    public function __isset($name)
    {
        $return = false;
        
        if ($name == 'value') {
//            fb::log('Checking if value is set for ' . $this->name . '...');

            $value = $this->_value;

//            fb::log($value, '$value');

            //---- Process for empty() PHP function
            if ($value === 0 && !is_null($value))
            {
//                fb::log('-- FALSE! --');
                #---- return false so that empty() works correctly with 0 numbers.
            } else {
                $return = $value != '';
            }

//           fb::log($return, '$return');

        } else {
            $return = parent::__isset($name);
        }
        
        return $return;
    }

    public function __set($name, $value)
    {
        if ($name == 'value') {
            if ($this->_value != $value)
            {
                $this->priorValue = $this->_value;
                $this->changed = true;
            }

            $this->_value = $value;
        } else {
            throw new Exception('Invalid property "' . $name . '"!');
        }
    }

    public function __toString()
    {
        return $this->_value;
    }
}



?>
