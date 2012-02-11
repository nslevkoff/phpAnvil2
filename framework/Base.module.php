<?php
/**
* @file
*
*/

require_once(PHPANVIL_TOOLS_PATH . 'atObject.abstract.php');


class BaseModule extends atObjectAbstract {

    const TYPE_CORE     = 1;
    const TYPE_CUSTOM   = 2;


    public $type = self::TYPE_CUSTOM;
    public $name = 'unknown';
    public $refName = 'unknown';
    public $version = '1.0';
    public $build = '1';

    public $defaultController = 'Dashboard';


    protected $_isInitialized = false;
    protected $_isOpened = false;


	function __construct() {

		return true;
	}


    function init()
    {
        global $phpAnvil;

        if (!$this->_isInitialized)
        {
            $phpAnvil->triggerEvent('module.init', array('module' => $this->refName));
            $this->_isInitialized = true;
        }

        return true;
    }


    function open()
    {
        global $phpAnvil;

        if (!$this->_isOpened)
        {
            $phpAnvil->triggerEvent('module.open', array('module' => $this->refName));
            $this->_isOpened = true;
        }

        return true;
    }


    function close()
    {
        global $phpAnvil;

        if ($this->_isOpened)
        {
            $phpAnvil->triggerEvent('module.close', array('module' => $this->refName));
            $this->_isOpened = false;
        }

        return true;
    }



    function buildConfigContent()
    {
    }


	function loadModuleID($id) {
		global $moduleCode;

		require_once(MODULES_PATH . $moduleCode[$id] . '.module.php');
	}


	function loadModuleCode($code) {

		require_once(MODULES_PATH . $code . '.module.php');
	}


	function processAction(Action $action) {
		return true;
	}


    function register(ModuleModel $objModule)
    {
        return true;
    }

}

?>