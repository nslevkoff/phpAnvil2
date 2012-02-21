<?php
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php');

class BaseWebAction extends anvilObjectAbstract {

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