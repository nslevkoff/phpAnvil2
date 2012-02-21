<?php
//require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';
require_once 'Base.controller.php';


class BasePlugin extends BaseController
{

    public $id = 1;
    public $controller;

	function __construct()
    {

        parent::__construct();

//        $this->_controller = $controller;


		return true;
	}


    function init()
    {
//        global $phpAnvil;

        $return = true;

        $this->pagePath = $this->controller->pagePath;

        return $return;
    }


    function open()
    {
//        global $phpAnvil;

        $return = true;

        return $return;
    }


    function close()
    {
//        global $phpAnvil;

        return true;
    }
}

?>