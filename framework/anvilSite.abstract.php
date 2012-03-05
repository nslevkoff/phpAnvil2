<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';


abstract class anvilSiteAbstract extends anvilObjectAbstract
{
    const ENVIRONMENT_DEVELOPMENT   = 1;
    const ENVIRONMENT_STAGING       = 2;
    const ENVIRONMENT_PRODUCTION    = 3;


    public $configFilename;
    public $environment = self::ENVIRONMENT_DEVELOPMENT;
    public $timeZone = 'UTC';
    public $path = SITE_PATH;
    public $webPath;


	function __construct()
    {

        //---- Unset defined properties before defining virtual versions
        //---- of the properties
//        unset($this->configFilename);
//        unset($this->environment);
//        unset($this->timeZone);
//        unset($this->path);
//        unset($this->webPath);


        //---- Define virtual properties.
//        $this->addProperty('configFilename', 'site.config.php');
//        $this->addProperty('environment', self::ENVIRONMENT_DEVELOPMENT);
//        $this->addProperty('timeZone', 'America/Los_Angeles');
//
//        $this->addProperty('path', SITE_PATH);
//        $this->addProperty('webPath', '.');

		return true;
	}


    function init()
    {
        global $phpAnvil;

        $this->loadConfig();

        $phpAnvil->triggerEvent('site.init');
        return true;
    }


    function open()
    {
        global $phpAnvil;

        $phpAnvil->triggerEvent('site.open');
        return true;
    }


    function close()
    {
        global $phpAnvil;

        $phpAnvil->triggerEvent('site.close');
        return true;
    }


    function loadConfig()
    {
        $return = false;

        $filePath = APP_PATH . $this->configFilename;
        if (file_exists($filePath))
        {
            include_once $filePath;
            $return = true;
        }

        return $return;
    }


}

?>