<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/

require_once('atObject.abstract.php');

/**
* Base Dynamic Object Class
*
* This class provides support for dynamic properties and methods.
*
* @version		1.0
* @date			8/24/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class atDynamicObjectAbstract extends atObjectAbstract {
	/**
	* Version number for this class release.
	*
	*/
	const VERSION		= '1.0';

	protected $_properties = array();
	protected $_values = array();
	protected $_changed = array();
	protected $_callbacks = array();


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
	public function __get($name) {
		#---- Validate Whether Property Exists
		if (!array_key_exists($name, $this->_properties)) {
			throw new Exception('Invalid property "' . $name . '"!');
		}

		#---- Use Custom Function Override if Exists, Otherwise Return Value
		if (method_exists($this, 'get' . $name)) {
			$return = call_user_func(array($this, 'get' . $name));
		} else {
			#---- If Value Doesn't Exist, Use Default
			if (!array_key_exists($name, $this->_values)) {
				$this->_values[$name] = $this->_properties[$name];
			}
			$return = $this->_values[$name];
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
	public function __set($name, $value) {

		#---- Validate Whether Property Exists
		if (!array_key_exists($name, $this->_properties)) {
			throw new Exception('Invalid property "' . $name . '"!');
		}

		#---- Use Custom Function Override if Exists, Otherwise Return Value
		if (method_exists($this, 'set' . $name)) {
			return call_user_func(array($this, 'set' . $name), $value);
		} else {

			if ($this->_properties[$name] != $value && !array_key_exists($name, $this->_changed)) {
				$this->_changed[] = $name;
			}

			$this->_values[$name] = $value;
		}
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
	public function __isset($name) {
		$return = false;


		if (!array_key_exists($name, $this->_properties)) {
			$this->enableTrace();
			$this->addTraceInfo(__FILE__, get_class($this) . '::' . __METHOD__, __LINE__, 'Invalid property "' . $name . '"!', self::TRACE_TYPE_ERROR);
		} else {

			$value = $this->_values[$name];


			#---- Validate Whether Property Exists
			$return = array_key_exists($name, $this->_properties);

			if ($return) {
				if ($value == 0 && !is_null($value)) {
					#---- return false so that empty() works correctly with 0 numbers.
				} else {
					$return = !empty($value);
				}
			}
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
    * @see atDynamicObjectAbstract::executeCallback
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
	protected function addProperty($name, $defaultValue = null) {
		$this->_properties[$name] = $defaultValue;
		$this->_values[$name] = $defaultValue;
	}


	/**
	* Resets the changed property status.
	*/
	public function resetChangedProperties() {
		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Resetting changed status of all properties...', self::TRACE_TYPE_INFO, self::TRACE_LEVEL_VERBOSE);
		$this->_changed = array();
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
	protected function executeCallback($name, $parameters = '')
    {
		if (array_key_exists($name, $this->_callbacks))
        {
            if (is_array($parameters))
            {
                return call_user_func_array($this->_callbacks[$name], $parameters);
            } else {
			    return call_user_func($this->_callbacks[$name], $parameters);
            }
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
	public function importProperties($properties = null) {

		if (is_array($properties)) {
			foreach($properties as $name => $newValue) {
				if (!array_key_exists($name, $this->_properties)) {
					$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Invalid property "' . $name . '"!', self::TRACE_TYPE_ERROR, self::TRACE_LEVEL_ERRORS);
				} else {
					$this->_values[$name] = $newValue;
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
	public function isChanged($name) {
		return array_key_exists($name, $this->_changed);
	}


	/**
	* Determines if a dynamic property has no value.
	*
	* @param $name
    *   A string containing the name of the dynamic property to check if
    *   a value exists.
	* @return (boolean) True if the property has NO value.
	*/
	public function isEmpty($name) {
		return empty($this->_values[$name]);
	}


	public function isProperty($name) {
		return array_key_exists($name, $this->_properties);
	}


	/**
	* Resets all of the property values to their defaults.
	*
	*/
	public function resetProperties() {
		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Resetting properties to defaults...', self::TRACE_TYPE_INFO, self::TRACE_LEVEL_VERBOSE);
		$this->_values = $this->_properties;
	}


	/**
	* Returns values for all dynamic properties as an array.
	*
	* @return array Values for all dynamic properties.
	*/
	public function toArray() {
		return $this->_values;
	}

}

?>
