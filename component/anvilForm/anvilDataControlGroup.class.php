<?php
require_once 'anvilControlGroup.class.php';


/**
 * Fieldset Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilDataControlGroup extends anvilControlGroup
{

    /**
     * @var anvilModelField
     */
    public $field;


    public function __construct($label, anvilModelField $field, $properties = null)
    {

        $this->enableLog();

        $this->field = $field;

        $labelForID = $field->formName . '[' . $field->name . ']';

        parent::__construct('', $label, $labelForID, $properties);

    }

}

?>