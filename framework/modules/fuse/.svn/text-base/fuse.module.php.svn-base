<?php

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.module.php');
//require_once(PHPANVIL_FRAMEWORK_PATH . 'EventListener.class.php');

require_once('fuse.inc.php');


class FuseModule extends BaseModule
{

	const NAME			= 'Fuse Module';
	const CODE			= 'Fuse';
	const VERSION		= '1.0';
	const VERSION_BUILD = '1';
	const VERSION_DTS	= '8/17/2011 4:30:00 PM PST';


	function __construct()
    {
		$this->enableTrace();

		$return = parent::__construct();

        $this->defaultController = '';

		return $return;
	}

    function init()
    {
//        global $phpAnvil;

        $return = parent::init();

        return $return;
    }


}

$phpAnvil->module['fuse'] = new FuseModule();

?>