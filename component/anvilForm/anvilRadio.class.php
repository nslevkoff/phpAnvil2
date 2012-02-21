<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/


require_once('anvilFormControl.abstract.php');


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

	
	public $checked;
	public $disabled;
	public $text;
	public $value;

    public $onClick = '';
	
	
	public function __construct($id = '', $name = '', $value = '', $text = '', $checked = false, $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		unset($this->checked);
		unset($this->disabled);
		unset($this->text);
		unset($this->value);
		
		
		$this->addProperty('checked', false);
		$this->addProperty('disabled', false);
		$this->addProperty('text', '');
		$this->addProperty('value', '');

		$this->text = $text;
		$this->checked = $checked;
		$this->value = $value;

		parent::__construct($id, $name, $properties, $traceEnabled);
	}

	public function renderContent() {
		$return = '<input type="radio"';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

		if ($this->value) {
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

		$return .= ' />';

		if ($this->text) {
			$return .= $this->text;
		}

		return $return;
	}

	public function renderPreClientScript() {
		$return = '';
		$return .= parent::renderPreClientScript();
		return $return;
	}

	public function renderPostClientScript() {
		$return = '';
		$return .= parent::renderPostClientScript();
		return $return;
	}
}

?>