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


require_once('anvilControl.abstract.php');


/**
* Literal Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilLiteral extends anvilControlAbstract {
	
	const VERSION        = '1.0';


	public $value;
	
	
	public function __construct($id = '', $value = '', $properties = null) {

//		unset($this->value);
		

//		$this->addProperty('value', '');

		parent::__construct($id, $properties);

		$this->value = $value;

	}

	public function renderContent() {
		return $this->value;
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