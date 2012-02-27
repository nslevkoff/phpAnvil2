<?php

require_once 'anvilComboBox.class.php';


/**
 * Text Entry Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataComboBox extends anvilComboBox
{
    public $field;

    public function __construct($field, $properties = null)
    {

//        $this->enableLog();

        $this->field = $field;

        $id = $field->tableName . '_' . $field->name;
        $name = $field->tableName . '[' . $field->name . ']';

        parent::__construct($id, $name, $field->value, $properties);
    }


}

?>