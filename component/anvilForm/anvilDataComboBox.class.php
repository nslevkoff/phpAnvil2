<?php

require_once 'anvilComboBox.class.php';


/**
 * Text Entry Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataComboBox extends anvilComboBox
{
    /**
     * @var anvilModelField
     */
    public $field;

    public function __construct(anvilModelField $field, $size = self::SIZE_MEDIUM, $properties = null)
    {

//        $this->enableLog();

        $this->field = $field;

        $id = $field->formName . '_' . $field->name;
        $name = $field->formName . '[' . $field->name . ']';

        parent::__construct($id, $name, $size, $field->value, $properties);
    }


}

?>