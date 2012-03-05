<?php

require_once 'anvilCheckBox.class.php';


/**
 * Data Checkbox Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataCheckBox extends anvilCheckBox
{
    public $field;

    public function __construct($field, $text, $properties = null)
    {

//        $this->enableLog();

        $this->field = $field;

//        $id = $field->tableName . '_' . $field->name;
        $id = '';
        $name = $field->tableName . '[' . $field->name . ']';

        parent::__construct($id, $name, $text, $field->value, $properties);
    }


}

?>