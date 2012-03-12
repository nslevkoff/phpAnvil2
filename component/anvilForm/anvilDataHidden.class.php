<?php
require_once 'anvilHidden.class.php';


/**
* Text Entry Control
*
* @copyright 	Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
*/
class anvilDataHidden extends anvilHidden {

    /**
     * @var anvilModelField
     */
    public $field;

    public function __construct(anvilModelField $field, $properties = null)
    {

//        $this->enableLog();

        $this->field = $field;

        $id = $field->formName . '_' . $field->name;
        $name = $field->formName . '[' . $field->name . ']';

        parent::__construct($id, $name, $field->value, $properties);
    }


}

?>