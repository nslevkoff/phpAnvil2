<?php
/**
* @file
*/

require_once(PHPANVIL_TOOLS_PATH . 'atDynamicObject.abstract.php');


class BaseApplication extends atDynamicObjectAbstract
{

    public $name;
    public $refName;
    public $version;
    public $build;
    public $copyright;
    public $copyrightHTML;

    public $configFilename;

    public $catchAllModule;
    public $catchAllAction;
    public $defaultModule;
    public $defaultAction;
    public $loginModule;
    public $loginAction;
    public $requestedModule;
    public $requestedAction;

	function __construct()
    {

        //---- Unset defined properties before defining virtual versions
        //---- of the properties
        unset($this->name);
        unset($this->refName);
        unset($this->version);
        unset($this->build);
        unset($this->copyright);
        unset($this->copyrightHTML);

        unset($this->configFilename);

        unset($this->defaultModule);
        unset($this->defaultAction);
        unset($this->requestedModule);
        unset($this->requestedAction);


        //---- Define virtual properties.
        $this->addProperty('name', 'New Application');
        $this->addProperty('refName', 'App');
        $this->addProperty('version', '1.0');
        $this->addProperty('build', '1');
        $this->addProperty('copyright', '(c) 2012');
        $this->addProperty('copyrightHTML', '&copy; 2012');

        $this->addProperty('configFilename', 'application.config.php');

        $this->addProperty('defaultModule', 'phpAnvil');
        $this->addProperty('defaultAction', 'modules');
        $this->addProperty('requestedModule', $this->defaultModule);
        $this->addProperty('requestedAction', $this->defaultAction);

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
        } else {
            FB::error('Site not set in phpAnvil.');
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;

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