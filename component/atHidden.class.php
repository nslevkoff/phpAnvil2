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


require_once('atFormControl.abstract.php');


/**
* Text Entry Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atHidden extends atFormControlAbstract {

	const VERSION        = '1.0';


	public $value;
	
	
	public function __construct($id = '', $name = '', $value = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		unset($this->value);
		
		
		$this->addProperty('value', '');

		$this->value = $value;

		parent::__construct($id, $name, $properties, $traceEnabled);
	}


	public function renderHTML() {
		return $this->render();
	}


	public function renderContent() {
		$return = '<input type="hidden"';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}


		$return .= ' value="' . $this->value . '"';


		$return .= " />\n";

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