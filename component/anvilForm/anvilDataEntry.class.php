<?php

require_once 'anvilEntry.class.php';


/**
 * Text Entry Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataEntry extends anvilEntry
{
    public $field;

    public function __construct($field, $size = self::SIZE_MEDIUM, $properties = null)
    {

        $this->enableLog();

        $this->field = $field;

        $id = $field->tableName . '_' . $field->name;
        $name = $field->tableName . '[' . $field->name . ']';

        parent::__construct($id, $name, $size, $field->value, $properties);
    }


}

?>