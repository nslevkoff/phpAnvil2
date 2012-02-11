<?php
/**
* @file
* phpAnvilTools Regional Class
*
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011-2012 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools atRegional
*/


require_once('atObject.abstract.php');

/**
* Primary atRegional Class
*
* @version		1.0
* @date			1/21/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atRegional
*/
class atRegional extends atObjectAbstract
{
	const VERSION		= '1.1';


    public $dateFormat = 'm/d/Y';
    public $dtsFormat = 'm/d/Y h:i:s A';
    public $dateTimeZone;

    //---- Depreciated, use $regional->dateTimeZone->getOffset()
    public $timezoneOffset = 0;

	public function __construct()
    {
        //---- Create DateTimeZone defaulting to PST ---------------------------
        //-- Change timezones using $regional->dateTimeZone->setTimezone($timezone)
        $this->dateTimeZone = new DateTimeZone('PST');
	}

    public function setTimezone($timezone)
    {
        $this->dateTimeZone = new DateTimeZone($timezone);

        return true;
    }


}

?>