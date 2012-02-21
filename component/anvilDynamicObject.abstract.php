<?php
require_once 'anvilObject.abstract.php';
require_once 'anvilDynamicProperties.class.php';

/**
* Base Dynamic Object Class
*
* @copyright 	Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
*/
abstract class anvilDynamicObjectAbstract extends anvilObjectAbstract
{
    const VERSION = '2.0';


    public $autoAddOnSet = true;
    public $properties;

//	protected $_properties = array();
//	protected $_values = array();
//	protected $_changed = array();
	protected $_callbacks = array();


    public function __construct()
    {
        parent::__construct();

//        $this->enableLog();

        $this->_newProperties();
    }


    protected function _newProperties()
    {
        if (!isset($this->properties))
        {
            $this->properties = new anvilDynamicProperties();
        }
    }

	/**
	* Returns the value of a dynamic property. Will automatically use a
    * "get" accessor function if it exists.
	*
	* @param $name
    *   A string containing the name of the property to get the value from.
    *
    * @exception string Invalid property!
    *
	* @return
    *   Returns the value of the dynamic property or the assigned default
    *   value.
	*/
	public function __get($name)
    {
        $return = null;

		#---- Validate Whether Property Exists
        $isValid = $this->properties->exists($name);

        if ($isValid)
        {

		    #---- Use Custom Function Override if Exists, Otherwise Return Value
		    if (method_exists($this, 'get' . $name))
            {
			    $return = call_user_func(array($this, 'get' . $name));
		    } else {
//                $return = $this->properties->property($name);

			    #---- If Value Doesn't Exist, Use Default
                $return = $this->properties->property($name)->value;

//                $this->_logdebug('|' . $return . '|', $name . '=');

//                if (empty($return))
                if ($return === '')
                {
//                    $this->_logDebug('using default value...');
                    $return = $this->properties->property($name)->defaultValue;
                }
		    }
        } else {
            throw new Exception('Invalid property "' . $name . '"!');
        }

		return $return;
	}


	/**
	* Sets the value of a dynamic property. Will automatically use a "set"
    * accessor function if it exists.
	*
	* @param $name
    *   A string containing the name of the property to set the value to.
	* @param $value
    *   The value to set the dynamic property to.
    *
    * @exception string Invalid property!
	*/
	public function __set($name, $value)
    {
//        fb::log($this->properties, 'properties');

        #---- Validate Whether Property Exists
        $isValid = $this->properties->exists($name);

        if ($isValid)
        {
		    #---- Use Custom Function Override if Exists, Otherwise Set Value
		    if (method_exists($this, 'set' . $name))
            {
			    return call_user_func(array($this, 'set' . $name), $value);
		    } else {

			    if ($this->properties->property($name)->value != $value)
                {
                    $this->properties->property($name)->priorValue = $this->properties->property($name)->value;
				    $this->properties->property($name)->changed = true;
			    }

			    $this->properties->property($name)->value = $value;
		    }
        } else {
//            throw new Exception('Invalid property "' . $name . '"!');

            //---- Property is automatically added if a new $name
            $property = $this->properties->property($name, true);
            $property->name = $name;
//            $property->defaultValue = $defaultValue;
            $property->value = $value;

            $this->_logDebug($this->properties);
        }

        return true;
	}


	/**
	* Returns whether a dynamic property has its value set.
	*
	* @param $name
    *   A string containing the name of the property to check if a value
    *   is set.
	*
	* @return
    *   Returns TRUE if the property's value is set, otherwise FALSE.
	*/
	public function __isset($name)
    {
		$return = $this->properties->exists($name);

		if ($return)
        {

			$value = $this->properties->property($name)->value;

            //---- Process for empty() PHP function
			if ($value == 0 && !is_null($value))
            {
				#---- return false so that empty() works correctly with 0 numbers.
			} else {
				$return = !empty($value);
			}
        } else {
            throw new Exception('Invalid property "' . $name . '"!');
        }

		return $return;
	}


	/**
	* Adds a custom callback function to the object, which will be executed
    * later.
	*
	* @param $name
    *   A string containing the name of the callback function.
	* @param $function
    *   A reference to the callback function itself.
    *
    * @see anvilDynamicObjectAbstract::executeCallback
	*/
	protected function addCallback($name, $function) {
		$this->_callbacks[$name] = $function;
	}


	/**
	* Adds a dynamic property to the object.
	*
	* @param $name
    *   A string containing the name of the new dynamic property.
	* @param $defaultValue
    *   (optional) The value to use as the default value for the dynamic
    *   property. [null]
	*/
/*
	protected function addProperty($name, $defaultValue = null)
//    protected function addProperty($name)
    {
        if (!isset($this->properties))
        {
            $this->_newProperties();
        }

        //---- Property is automatically added if a new $name
        $property = $this->properties->property($name, true);
        $property->name = $name;
        $property->defaultValue = $defaultValue;
        $property->value = $defaultValue;

        return $property;
	}
*/

	/**
	* Resets the changed property status.
	*/
	public function resetChangedProperties() {
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Resetting changed status of all properties...', self::TRACE_TYPE_INFO, self::TRACE_LEVEL_VERBOSE);
//		$this->_changed = array();
        $this->properties->resetChanged();
	}


	/**
	* Executes a dynamic callback previously added using the addCallback
    * method.
	*
	* @param $name
    *   A string containing the name of the callback to execute.
	* @param $parameters
    *   (optional) An array of paramters to use with the callback function.
    *
	* @return
    *   Returns the The callback's response or FALSE if unable to execute
    *   the callback.
	*/
	protected function executeCallback($name, $parameters = '') {
		if (array_key_exists($name, $this->_callbacks)) {
			return call_user_func($this->_callbacks[$name], $parameters);
		} else {
			return false;
		}
	}


	/**
	* Imports an array of dynamic property values.
	*
	* @param $properties
    *   (optional) An array of dynamic property values to set.
	*/
	public function importProperties($properties = null)
    {

		if (is_array($properties))
        {
			foreach($properties as $name => $newValue)
            {
                if ($this->properties->exists($name))
                {
                    if ($this->properties->property($name)->value != $newValue)
                    {
                        $this->properties->property($name)->priorValue = $this->properties->property($name)->value;
                        $this->properties->property($name)->changed = true;
                    }

                    $this->properties->property($name)->value = $newValue;
                }
			}
		}
	}


	/**
	* Determines if a dynamic property has changed.
	*
	* @param $name
    *   A string containing the name of the dynamic property to check if
    *   the value has changed.
	* @return
    *   Returns TRUE if the property value has changed, otherwise FALSE.
	*/
//	public function isChanged($name)
//    {
//		return array_key_exists($name, $this->_changed);
//	}


	/**
	* Determines if a dynamic property has no value.
	*
	* @param $name
    *   A string containing the name of the dynamic property to check if
    *   a value exists.
	* @return (boolean) True if the property has NO value.
	*/
//	public function isEmpty($name) {
//		return empty($this->_values[$name]);
//	}


	public function isProperty($name)
    {
        return $this->properties->exists($name);
	}


	/**
	* Resets all of the property values to their defaults.
	*
	*/
	public function resetProperties()
    {
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Resetting properties to defaults...', self::TRACE_TYPE_INFO, self::TRACE_LEVEL_VERBOSE);
//		$this->_values = $this->properties;
        $this->properties->reset();
	}


	/**
	* Returns values for all dynamic properties as an array.
	*
	* @return array Values for all dynamic properties.
	*/
	public function toArray()
    {
		return $this->properties->toArray();
	}

}

?>
