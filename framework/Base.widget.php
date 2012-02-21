<?php
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php');

class BaseWidget extends anvilContainer {

    public $name;
    public $refName;
    public $version;
    public $build;

	function __construct($id = '')
    {
		parent::__construct($id);

		$this->enableTrace();

        $this->name = 'New Widget';
        $this->refName = 'widget';
        $this->version = '1.0';
        $this->build = '1';

		return true;
	}

}

?>