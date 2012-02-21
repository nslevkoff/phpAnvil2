<?php
/**
* @file
* phpAnvil Database Collection
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup      phpAnvil
*/


require_once(PHPANVIL2_COMPONENT_PATH . 'anvilCollection.class.php');


/**
*
* Database Collection Class
*
* @version      v1.0.1
* @date         10/12/2010
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      phpAnvil
*/
class DatabaseCollection extends anvilCollection {

	const VERSION	= '1.0';
	const BUILD		= '1';


	public function __construct() {
		$this->enableTrace();
	}


    public function offsetGet($offset)
    {
        global $phpAnvil;

        $return = false;

        if (!$this->exists($offset))
        {
            $msg = 'Database, ' . $offset . ', not found.';
            $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_DEBUG);
            FB::log($msg);
        } else
        {
            $return = parent::offsetGet($offset);
        }

        return $return;
    }



}

?>
