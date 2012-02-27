<?php

require_once 'anvilMemo.class.php';


/**
 * Data Memo Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataMemo extends anvilMemo
{
    public $field;

    public function __construct($field, $size = self::SIZE_MEDIUM, $rows = 3, $properties = null)
    {

//        $this->enableLog();

        $this->field = $field;

        $id = $field->tableName . '_' . $field->name;
        $name = $field->tableName . '[' . $field->name . ']';

        parent::__construct($id, $name, $size, $rows, $field->value, $properties);
    }


}

?>