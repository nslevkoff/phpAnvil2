<?php
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php');


abstract class anvilApplicationAbstract extends anvilObjectAbstract
{

    public $name = 'New Application';
    public $refName = 'App';
    public $version = '0.1';
    public $build = '1';
    public $copyright = '(c) 2012';
    public $copyrightHTML = '&copy; 2012';

    public $cookieAccountToken = '_anvila';
    public $cookieUserID = '_anvilu';
    public $cookieUserToken = '_anvilt';

    public $configFilename = 'application.config.php';

    public $catchAllModule;
    public $catchAllAction;
    public $defaultModule = 'phpAnvil';
    public $defaultAction;
    public $loginModule;
    public $loginAction;
    public $requestedModule = 'phpAnvil';
    public $requestedAction;

    public $defaultURL;
    public $loginURL;

    public $account;

    /**
     * @var anvilUserModelAbstract
     */
    public $user;

    //---- Application Encryption Key - OVERRIDE PER APPLICATION ---------------
    public $cryptKey = 'anvil';


	function __construct()
    {
		return true;
	}


    function init()
    {
        global $phpAnvil;

        $return = false;

        $this->loadConfig();

        $phpAnvil->triggerEvent('application.init');


        //---- Check if Site is Set
        if (isset($phpAnvil->site))
        {
            //---- Initialize the Site
            $phpAnvil->site->init();
            $return = true;

            $this->defaultURL = $phpAnvil->site->webPath;
            $this->loginURL = $this->defaultURL . 'Login/';

        } else {
            FB::error('Site not set in phpAnvil.');
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;

        $return = false;

        $phpAnvil->triggerEvent('application.open');


        //---- Check if Site is Set
        if (isset($phpAnvil->site))
        {
            //---- Initialize the Site
            $phpAnvil->site->open();
            $return = true;
        } else {
            FB::error('Site not set in phpAnvil.');
        }

        return $return;
    }


    function close()
    {
        global $phpAnvil;

        //---- Check if Site is Set
        if (isset($phpAnvil->site))
        {
            //---- Initialize the Site
            $phpAnvil->site->close();
            $return = true;
        } else {
            FB::error('Site not set in phpAnvil.');
        }

        $phpAnvil->triggerEvent('application.close');

        return $return;
    }


    function authenticateUser()
    {
        global $phpAnvil;

        $phpAnvil->triggerEvent('application.authenticateUser');

        $return = $phpAnvil->userAuthenticated;

        return $return;
    }


    function loadConfig()
    {
        global $phpAnvil;

        $return = false;

        $filePath = APP_PATH . $this->configFilename;
        if (file_exists($filePath))
        {
            include_once $filePath;

            FB::info('Application config file, ' . $this->configFilename . ', loaded.');

            $return = true;
        }
    }
}

?>