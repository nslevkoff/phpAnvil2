<?php

require_once 'anvilEntry.class.php';


/**
 * Text Entry Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataEntry extends anvilEntry
{
    /**
     * @var anvilModelField
     */
    public $field;

    public function __construct(anvilModelField $field, $size = self::SIZE_MEDIUM, $properties = null)
    {

//        $this->enableLog();
//        $this->_logDebug($field, '$field');
//        $this->_logDebug($field->value, '$field->value');

        $this->field = $field;

        $id = $field->formName . '_' . $field->name;
        $name = $field->formName . '[' . $field->name . ']';

        parent::__construct($id, $name, $size, $field->value, $properties);

        $this->required = $field->required;
    }


}

?>