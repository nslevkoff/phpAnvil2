<?php
/**
* @file
* phpAnvil Controller Collection
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
* Controller Collection Class
*
* @version      v1.0.1
* @date         10/12/2010
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      phpAnvil
*/
class ControllerCollection extends atCollection
{

	const VERSION	= '1.0';
	const BUILD		= '1';


	public function __construct() {
		$this->enableTrace();
	}


    public function offsetGet($offset)
    {
        global $phpAnvil;
        global $firePHP;

        $return = false;

        if (!$this->exists($offset))
        {
            $msg = 'Controller (' . $offset . ') not found.';
                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_ERROR);
            FB::error($msg);
        } else
        {
            $return = parent::offsetGet($offset);
        }

        return $return;
    }



}

?>
