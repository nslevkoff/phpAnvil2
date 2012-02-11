<?php
/**
* @file
* phpAnvil Modules Collection
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup      phpAnvil
*/


require_once(PHPANVIL_TOOLS_PATH . 'atCollection.class.php');


/**
*
* Modules Collection Class
*
* @version      v1.0.1
* @date         10/5/2010
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      phpAnvil
*/
class ModuleCollection extends atCollection {

	const VERSION	= '1.0';
	const BUILD		= '1';


	public function __construct() {
		$this->enableTrace();
	}


    public function offsetGet($moduleRefName)
    {
        global $phpAnvil;
//        global $firePHP;

        $moduleRefName = strtolower($moduleRefName);

//        FB::log($offset);
//        FB::log($this->_items);

        //---- Is the module not already loaded?
//        if (!isset($this->_items[$offset])) {
        if (!$this->exists($moduleRefName))
        {
            //---- Load the module
            $phpAnvil->loadModule($moduleRefName);
        }

        return parent::offsetGet($moduleRefName);
    }



}

?>
