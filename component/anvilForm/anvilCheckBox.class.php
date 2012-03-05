<?php

require_once 'anvilFormControl.abstract.php';


/**
 * CheckBox Form Control
 *
 * @copyright       Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilCheckBox extends anvilFormControlAbstract
{

    const VERSION = '1.0';


    public $checked = false;
    public $disabled = false;
    public $text;
    public $valueChecked = 1;
    public $valueUnchecked = 0;


    public function __construct($id = '', $name = 'checkbox', $text = '** Undefined CheckBox **', $checked = false, $properties = array())
    {

        $this->text    = $text;
        $this->checked = $checked;

        parent::__construct($id, $name, $properties);
    }


    public function renderContent()
    {
        $return = '';

        //==== HIDDEN ==========================================================
//        if (!$this->checked) {
            $return .= '<input type="hidden"';
            $return .= ' name="' . $this->name . '"';
            $return .= ' value="' . $this->valueUnchecked . '"';
            $return .= '>';
//        }

        //==== LABEL ===========================================================
        //---- Class -----------------------------------------------------------
        $return .= '<label class="checkbox';

        if ($this->class) {
            $return .= ' ' . $this->class;
        }
        $return .= '"';

//        $return .= ' for="' . $this->name . '"';

        $return .= '>';

        //==== INPUT ===========================================================
        $return .= '<input type="checkbox"';

        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        $return .= ' name="' . $this->name . '"';

        $return .= ' value="' . $this->valueChecked . '"';

        if ($this->checked) {
            $return .= ' checked="checked"';
        }

        if ($this->disabled) {
            $return .= ' disabled="disabled"';
        }
        $return .= '>';

        $return .= $this->text;

        $return .= '</label>';

        return $return;
    }
}

?>