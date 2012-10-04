<?php

require_once 'anvilFormControl.abstract.php';


/**
* Radio Form Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilRadio extends anvilFormControlAbstract {

	const VERSION        = '1.0';

	
	public $checked = false;
	public $disabled = false;
	public $text;
	public $value;

    public $onClick = '';
	
	
	public function __construct($id = '', $name = '', $value = '', $text = '', $checked = false, $properties = array())
    {
		$this->text = $text;
		$this->checked = $checked;
		$this->value = $value;

		parent::__construct($id, $name, $properties);
	}

	public function renderContent() {
		$return = '<input type="radio"';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

		if (isset($this->value)) {
			$return .= ' value="' . $this->value . '"';
		}

		if ($this->checked) {
			$return .= ' checked="checked"';
		}

		if ($this->disabled) {
			$return .= ' disabled="disabled"';
		}

        $return .= $this->renderTriggers();
        

//        if ($this->_enableAjax) {
//            $return .= ' onClick="call_' . key($this->_options) . '();"';
//        }

        if (!empty($this->onClick))
        {
            $return .= ' onClick="' . $this->onClick . '"';
        }

        if ($this->class) {
            $return .= ' class="' . $this->class . '"';
        }

        if ($this->class) {
            $return .= ' class="' . $this->class . '"';
        }


        $return .= ' />';

		if ($this->text) {
			$return .= $this->text;
		}

		return $return;
	}

}

?>