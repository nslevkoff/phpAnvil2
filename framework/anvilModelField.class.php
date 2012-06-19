<?php
/**
 * @property mixed  $value
 * @property string $displayName
 */
class anvilModelField
{
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

//    public $model;

    public $name;
    protected $_displayName;
    public $defaultValue = null;
    protected $_value;
    public $changed = false;
    public $priorValue;

    public $tableName;
    public $formName;
    public $fieldName;
    public $fieldType = self::DATA_TYPE_STRING;
    public $maxLength;
    public $decimalPlace;
    public $allowNull = true;
    public $readOnly = false;
    public $dataRegEx;
    public $displayRegEx;

    public $formFieldName;
    public $required = false;
    public $validationRegEx;


    public function __construct($model, $name = '')
    {
        //        $this->model = $model;
        $this->name      = $name;
        $this->tableName = $model->primaryTableName;
        $this->formName  = $model->formName;
    }


    public function __get($name)
    {
        $return = null;

        switch (strtolower($name)) {
            case 'value':
                $return = $this->_value;
                break;

            case 'displayname':
                if ($this->_displayName <> '') {
                    $return = $this->_displayName;
                } else {
//                    $return = ucwords(str_replace('_', ' ', $this->name));

//                    $words = preg_split('/([[:upper:]][[:lower:]]+)/', $this->name, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    $return = ucwords(implode(' ', preg_split('/([[:upper:]][[:lower:]]+)/', $this->name, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));

                }
                break;
        }

        return $return;
    }


    public function __isset($name)
    {
        $return = false;

        switch (strtolower($name)) {
            case 'value':
                $value = $this->_value;

                //---- Process for empty() PHP function
                if ($value === 0 && !is_null($value)) {
                    #---- return false so that empty() works correctly with 0 numbers.
                } else {
                    $return = $value != '';
                }
                break;

            case 'displayname':
                $displayName = $this->_displayName;

                //---- Process for empty() PHP function
                if ($displayName === 0 && !is_null($displayName)) {
                    #---- return false so that empty() works correctly with 0 numbers.
                } else {
                    $return = $displayName != '';
                }
                break;

            default:
                $return = parent::__isset($name);
        }

        return $return;
    }


    public function __set($name, $value)
    {
        switch (strtolower($name)) {
            case 'value':
                if ($this->_value != $value) {
                    $this->priorValue = $this->_value;
                    $this->changed    = true;
                }

                $this->_value = $value;
                break;

            case 'displayname':
                $this->_displayName = $value;
                break;

            default:
                throw new Exception('Invalid property "' . $name . '"!');
        }
    }


    public function __toString()
    {
        return $this->_value;
    }


    /**
     * @param anvilDataConnectionAbstract $dataConnection
     *
     * @return DateTime|float|int|null|string
     */
    public function toSave($dataConnection)
    {
        $return = '';

        switch ($this->fieldType) {
            case self::DATA_TYPE_BOOLEAN:
                $return = $dataConnection->dbBoolean($this->_value);
                break;

            case self::DATA_TYPE_DATE:

                $value = isset($this->_value)
                        ? $this->_value
                        : ($this->allowNull
                                ? null
                                : $this->defaultValue);

                $return = $value;

                if (!is_null($value)) {
                    $value = new DateTime($value, new DateTimeZone('UTC'));

                    $return = $value->format($dataConnection->dateFormat);
                    $return = $dataConnection->dbDate($return);
                }

                break;
            case self::DATA_TYPE_DTS:

                $value = !empty($this->_value)
                        ? $this->_value
                        : ($this->allowNull
                                ? null
                                : $this->defaultValue);

                $return = $value;

                if (!is_null($value)) {
                    $value = new DateTime($value, new DateTimeZone('UTC'));

                    $return = $value->format($dataConnection->dtsFormat);
                    $return = $dataConnection->dbDTS($return);
                }

                break;

            case self::DATA_TYPE_PHONE:

                $value = '';

                if (isset($this->_value)) {
                    $pattern = '/[^0-9]*/';
                    $value   = preg_replace($pattern, '', $this->_value);
                }

                $return = $value != ''
                        ? $dataConnection->dbString($value)
                        : ($this->allowNull
                                ? null
                                : (isset($this->defaultValue)
                                        ? $dataConnection->dbString($this->defaultValue)
                                        : $dataConnection->dbString('')));

                if ($return == '') {
                    $return = null;
                }

                break;

            case self::DATA_TYPE_STRING:

                $return = isset($this->_value)
                        ? $dataConnection->dbString($this->_value)
                        : ($this->allowNull
                                ? null
                                : (isset($this->defaultValue)
                                        ? $dataConnection->dbString($this->defaultValue)
                                        : $dataConnection->dbString('')));

                if ($return == '') {
                    $return = null;
                }

                break;

            case self::DATA_TYPE_ADD_DTS:
                $return = 'NOW()';
                break;

            case self::DATA_TYPE_DECIMAL:
            case self::DATA_TYPE_FLOAT:
                $return = isset($this->_value)
                        ? floatval($this->_value)
                        :
                        ($this->allowNull
                                ? null
                                : (isset($this->defaultValue)
                                        ? $this->defaultValue
                                        : 0));
                break;

            case self::DATA_TYPE_NUMBER:
            default:

                $return = isset($this->_value)
                        ? intval($this->_value)
                        : ($this->allowNull
                                ? null
                                : (isset($this->defaultValue)
                                        ? $this->defaultValue
                                        : 0));
                break;
        }


        if (is_null($return)) {
            $return = 'null';
        }

        return $return;
    }

}


?>
