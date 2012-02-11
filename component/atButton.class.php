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
* Standard Form Based Button Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atButton extends atFormControlAbstract {

	const VERSION        = '1.0';


	const TYPE_SUBMIT		= 1;
	const TYPE_RESET		= 2;
	const TYPE_BUTTON		= 3;
	const TYPE_IMAGE		= 4;
	const TYPE_DELETE		= 4;

	
	public $confirmMsg;
	public $type;
	public $value;
	

	public function __construct($id = '', $name = 'btn', $type = self::TYPE_SUBMIT, $value = 'Submit', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		unset($this->confirmMsg);
		unset($this->type);
		unset($this->value);

		
		$this->addProperty('confirmMsg', '');
		$this->addProperty('type', self::TYPE_SUBMIT);
		$this->addProperty('value', 'Submit');

		$this->type = $type;
		$this->value = $value;

		parent::__construct($id, $name, $properties, $traceEnabled);
	}

	public function renderContent() {
		$return = '<input type="';

		switch ($this->type) {
			case self::TYPE_SUBMIT:
			case self::TYPE_DELETE:
				$return .= 'submit';
				break;
			case self::TYPE_RESET:
				$return .= 'reset';
				break;
			case self::TYPE_IMAGE:
				$return .= 'image';
				break;
			default:
				$return .= 'button';
				break;
		}

		$return .= '"';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

//		$return .= ' name="' . $this->name . '"';

		if ($this->value) {
			switch ($this->type) {
				case self::TYPE_SUBMIT:
				case self::TYPE_DELETE:
				case self::TYPE_RESET:
				case self::TYPE_BUTTON:
					$return .= ' value="' . $this->value . '"';
					break;
				case self::TYPE_IMAGE:
					$return .= ' src="' . $this->value . '"';
					break;
			}
		}

		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}

//		if ($this->type == self::TYPE_DELETE) {
		if ($this->confirmMsg) {
			$return .= " onclick=\"return confirm('" . $this->confirmMsg . "');\"";
		}

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