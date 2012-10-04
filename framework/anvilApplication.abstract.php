<?php
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php');


abstract class anvilApplicationAbstract extends anvilObjectAbstract
{

    /** @var phpAnvil2 */
    public $core;


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
        global $phpAnvil;

        $this->core = $phpAnvil;

        return true;
	}


    function init()
    {
//        global $phpAnvil;

        $return = false;

        $this->loadConfig();

//        $this->core->triggerEvent('application.init');


        //---- Check if Site is Set
        if (isset($this->core->site))
        {
            //---- Initialize the Site
            $this->core->site->init();
            $return = true;

            $this->defaultURL = $this->core->site->webPath;
            $this->loginURL = $this->defaultURL . 'Login/';

        } else {
            $this->_logError('Site not set in phpAnvil.');
//            FB::error('Site not set in phpAnvil.');
        }

        return $return;
    }


    function open()
    {
//        global $phpAnvil;

        $return = false;

//        $phpAnvil->triggerEvent('application.open');


        //---- Check if Site is Set
        if (isset($this->core->site))
        {
            //---- Initialize the Site
            $this->core->site->open();
            $return = true;
        } else {
//            FB::error('Site not set in phpAnvil.');
            $this->_logError('Site not set in phpAnvil.');
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
            $this->_logError('Site not set in phpAnvil.');
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