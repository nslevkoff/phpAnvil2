<?php

require_once 'anvilObject.abstract.php';

/**
* Primary anvilRegional Class
*
* @copyright 	Copyright (c) 2011-2012 Nick Slevkoff (http://www.slevkoff.com)
*/
class anvilRegional extends anvilObjectAbstract
{
	const VERSION		= '1.2';


    public $dateFormat = 'm/d/Y';
    public $dtsFormat = 'm/d/Y h:i:s A';
    public $dateTimeZone;

    //---- Depreciated, use $regional->dateTimeZone->getOffset()
    public $timezoneOffset = -8;

	public function __construct()
    {
        $this->enableLog();

        //---- Create DateTimeZone defaulting to PST ---------------------------
        //-- Change timezones using $regional->dateTimeZone->setTimezone($timezone)
        $this->dateTimeZone = new DateTimeZone('America/Los_Angeles');
	}

    public function setTimezone($timezone)
    {
        $this->_logVerbose('Setting regional timezone to ' . $timezone . '...');
        $this->dateTimeZone = new DateTimeZone($timezone);

        return true;
    }


}

?>