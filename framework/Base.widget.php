<?php
require_once(PHPANVIL_TOOLS_PATH . 'atContainer.class.php');

class BaseWidget extends atContainer {

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