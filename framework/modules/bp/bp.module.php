<?php
/**
*
* @file
* Background Processor (BP) Module Controller
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          BP_Module
*/

require_once 'BP.inc.php';

require_once PHPANVIL_FRAMEWORK_PATH . 'Base.module.php';

/**
*
* Background Processor (BP) Module Class
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          BP_Module
*/

class BPModule extends BaseModule
{


	function __construct()
    {
		$this->enableTrace();

		$return = parent::__construct();

        $this->type = self::TYPE_CORE;
        $this->name = 'Background Processor Module';
        $this->refName = 'BP';
        $this->version = '1.0';
        $this->build = '1';

		return $return;
	}

}


$phpAnvil->module['BP'] = new BPModule();

?>
