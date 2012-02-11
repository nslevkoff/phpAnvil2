<?php

require_once(PHPANVIL_TOOLS_PATH . 'atCollection.class.php');


/**
*
* Controller Plugin Collection Class
*
* @version      v1.0.1
* @date         10/1/2011
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
*/
class PluginCollection extends atCollection
{

	const VERSION	= '1.0';
	const BUILD		= '1';


	public function __construct() {
//		$this->enableTrace();
	}


    public function offsetGet($offset)
    {
//        global $phpAnvil;
//        global $firePHP;

        $return = false;

        if (!$this->exists($offset))
        {
            $msg = 'Controller Plugin (' . $offset . ') not found.';
            $this->logError($msg);
//                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_ERROR);
//            FB::error($msg);
        } else
        {
            $return = parent::offsetGet($offset);
        }

        return $return;
    }

}

?>
