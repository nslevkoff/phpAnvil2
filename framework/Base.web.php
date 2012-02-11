<?php
require_once(PHPANVIL_TOOLS_PATH . 'atObject.abstract.php');

class BaseWebAction extends atObjectAbstract {

	public $requiresLogin = true;

	function __construct() {
		return true;
	}


	function loadModules() {
		return true;
	}


	function Process() {
		return true;
	}

}

?>