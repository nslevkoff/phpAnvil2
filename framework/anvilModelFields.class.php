<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';

require_once 'anvilModelField.class.php';


class anvilModelFields extends anvilObjectAbstract
{

    protected $_fields = array();
    protected $_fieldIndex = array();

    //** anvilModelAbstract */
    public $model;


    public function __construct($model)
    {
        $this->enableLog();

        $this->model = $model;
    }


    public function exists($fieldName)
    {
        if (is_numeric($fieldName)) {
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
     * @param bool   $addIfNotExist
     *
     * @return anvilModelField
     */
    public function field($fieldName, $addIfNotExist = false)
    {
        $return = false;

//        $this->_logDebug($fieldName, '$fieldName');

        if (is_numeric($fieldName)) {
            $fieldName = $this->_fieldIndex[$fieldName];
        }

        if (array_key_exists($fieldName, $this->_fields)) {
            $return = $this->_fields[$fieldName];
        } else {
            if ($addIfNotExist) {
                $this->_fields[$fieldName] = $this->_newfield($fieldName);
                $this->_fieldIndex[]       = $fieldName;

                $return = $this->_fields[$fieldName];
            }
        }

        return $return;
    }


    public function &__get($fieldName)
    {

        if (!array_key_exists($fieldName, $this->_fields)) {
            $this->_fields[$fieldName] = $this->_newfield($fieldName);
            $this->_fieldIndex[]       = $fieldName;
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


    public function getChangedActivityArray()
    {
        $return       = array();
        $changedIndex = 0;

        $count = $this->count();

        for ($i = 0; $i < $count; $i++) {
            $field = $this->_fields[$this->_fieldIndex[$i]];
            /** @var $field anvilModelField **/

            if ($field->changed && $field->activity) {
                $return[$changedIndex]['name']        = $field->name;
                $return[$changedIndex]['fieldName']   = $field->fieldName;
                $return[$changedIndex]['displayName'] = $field->displayName;

                $return[$changedIndex]['from'] = $field->priorValue;
                $return[$changedIndex]['to']   = $field->value;

                $return[$changedIndex]['fromName'] = $field->priorValue;
                $return[$changedIndex]['toName']   = $field->value;

                if ($field->fieldType == anvilModelField::DATA_TYPE_INTEGER) {
//                    $this->_logDebug('Integer... checking for array name');
//                    $this->_logDebug($field->valueNameArray, '$field->valueNameArray');
//                    $this->_logDebug($field->priorValue, 'priorValue');
//                    $this->_logDebug($field->value, 'value');

                    if (isset($field->valueNameArray[$field->priorValue])) {
                        $return[$changedIndex]['fromName'] = $field->valueNameArray[$field->priorValue];
                    }

                    if (isset($field->valueNameArray[$field->value])) {
                        $return[$changedIndex]['toName'] = $field->valueNameArray[$field->value];
                    }

                } elseif ($field->fieldType == anvilModelField::DATA_TYPE_BOOLEAN) {
                    $return[$changedIndex]['fromName'] = (empty($field->priorValue)
                            ? 'Disabled'
                            : 'Enabled');

                    $return[$changedIndex]['toName'] = (empty($field->value)
                            ? 'Disabled'
                            : 'Enabled');
                }


                $changedIndex++;
            }
        }

        return $return;
    }


    public function getChangedArray()
    {
        $return       = array();
        $changedIndex = 0;

        $count = $this->count();

        for ($i = 0; $i < $count; $i++) {
            $field = $this->_fields[$this->_fieldIndex[$i]];
            /** @var $field anvilModelField **/

            if ($field->changed) {
                $return[$changedIndex]['name']        = $field->name;
                $return[$changedIndex]['fieldName']   = $field->fieldName;
                $return[$changedIndex]['displayName'] = $field->displayName;
                $return[$changedIndex]['from']        = $field->priorValue;
                $return[$changedIndex]['to']          = $field->value;

                $changedIndex++;
            }
        }

        return $return;
    }


    public function isChanged()
    {
        $count = $this->count();

//        $this->enableLog();
//        $this->_logDebug($count, 'isChanged count');
//        $this->_logDebug(json_encode($this->_fields), 'Fields');

        for ($i = 0; $i < $count; $i++) {
            $field = $this->_fields[$this->_fieldIndex[$i]];

//            $this->_logDebug($field->priorValue . ' -> ' . $field->value . ' (' . $field->changed . ')', $field->name);

//            if ($this->_fields[$this->_fieldIndex[$i]]->changed) {
            if ($field->changed) {
                return true;
            }
        }

        return false;
    }


    public function reset()
    {
        $count = $this->count();
        for ($i = 0; $i < $count; $i++) {
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

        for ($i = 0; $i < $count; $i++) {
            $this->_fields[$this->_fieldIndex[$i]]->changed = false;
        }
    }


    public function setFormName($name)
    {
        $count = $this->count();

        for ($i = 0; $i < $count; $i++) {
            $this->_fields[$this->_fieldIndex[$i]]->formName = $name;
        }
    }


    public function toArray()
    {
        $newArray = array();

//        $count = $this->count();

        foreach ($this->_fields as $name => $object) {
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
