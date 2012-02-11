<?php
/**
 * @file
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                  This source file is subject to the new BSD license that is
 *                  bundled with this package in the file LICENSE.txt. It is also
 *                  available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup         phpAnvilTools
 */


require_once('atDataObject2.abstract.php');


/**
 * Base Form Data Object Class
 *
 * This class adds form processing support to the data class.
 *
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools
 */
abstract class atFormObjectAbstract2 extends atDataObjectAbstract2
{
    /**
     * Version number for this class release.
     *
     */
    const VERSION = '2.1';

    const REQUEST_TYPE_GET  = 1;
    const REQUEST_TYPE_POST = 2;
    const REQUEST_TYPE_ALL  = 3;


//	protected $_formFields = array();


//    public function __construct(
//        $atDataConnection,
//        $atRegional,
//        $atDictionary,
//        $dataFrom = '',
//        $dataFilter = '')
//    {
//		parent::__construct($atDataConnection, $atRegional, $atDictionary, $dataFrom, $dataFilter);
//	}


    protected function newProperties()
    {
        if (!isset($this->properties)) {
            $this->properties = new atFormObjectProperties();
        }
    }


//	protected function addProperty($propertyName, $dataFieldName = 'undefined', $dataFieldType = self::DATA_TYPE_NUMBER, $defaultValue = null, $formFieldName = '') {
//		if (!empty($formFieldName)) {
//			$this->_formFields[$propertyName] = $formFieldName;
//		}
//		parent::addProperty($propertyName, $dataFieldName, $dataFieldType, $defaultValue);
//	}

    protected function addProperty(
        $name,
        $tableName = '',
        $fieldName = '',
        $fieldType = '',
        $defaultValue = null,
        $maxLength = 40,
        $allowNull = true,
        $readOnly = false,
        $formFieldName = null,
        $required = false)
    {
        $property = parent::addProperty(
            $name,
            $tableName,
            $fieldName,
            $fieldType,
            $defaultValue,
            $maxLength,
            $allowNull,
            $readOnly);

        if (!isset($formFieldName) && !$readOnly && $name != $this->idPropertyName) {
            $property->formFieldName = $name;
        } else {
            $property->formFieldName = $formFieldName;
        }
        $property->required = $required;

        return $property;
    }


    public function loadRequest($requestType = self::REQUEST_TYPE_POST, $processBooleans = false)
    {
        //        foreach ($this->_formFields as $propertyName => $formFieldName) {
        $this->resetChangedProperties();

        //        $this->logDebug($this->properties, '$this->properties');

        $count = $this->properties->count();
        for ($i = 0; $i < $count; $i++)
        {
            $property = $this->properties->property($i);

            if (!empty($property->formFieldName)) {
                switch ($requestType) {
                    case self::REQUEST_TYPE_GET:
                        if ($property->fieldType == self::DATA_TYPE_BOOLEAN && $processBooleans) {
                            $property->value = !empty($_GET[$property->formFieldName]);
                        } elseif (isset($_GET[$property->formFieldName]))
                        {
                            $property->value = $_GET[$property->formFieldName];
                        } elseif (!isset($property->value)) {
                            $property->value = $property->defaultValue;
                        }
                        break;

                    case self::REQUEST_TYPE_POST:
                        if ($property->fieldType == self::DATA_TYPE_BOOLEAN && $processBooleans) {
                            $property->value = !empty($_POST[$property->formFieldName]);
                        } elseif (isset($_POST[$property->formFieldName]))
                        {
                            $property->value = $_POST[$property->formFieldName];
                        } elseif (!isset($property->value)) {
                            $property->value = $property->defaultValue;
                        }
                        break;

                    case self::REQUEST_TYPE_ALL:
                    default:
                        if ($property->fieldType == self::DATA_TYPE_BOOLEAN && $processBooleans) {
                            $property->value = !empty($_REQUEST[$property->formFieldName]);
                        } elseif (isset($_REQUEST[$property->formFieldName]))
                        {
                            $property->value = $_REQUEST[$property->formFieldName];
                        } elseif (!isset($property->value)) {
                            $property->value = $property->defaultValue;
                        }
                        break;
                }

            }
        }

        return true;
    }

}


class atFormObjectProperties extends atDataObjectProperties
{
    protected function newProperty($propertyName = '')
    {
        return new atFormObjectProperty($propertyName);
    }
}


class atFormObjectProperty extends atDataObjectProperty
{
    public $formFieldName;
    public $required = false;
    public $validationRegEx;
}


?>
