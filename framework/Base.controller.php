<?php
require_once PHPANVIL_TOOLS_PATH . 'atDynamicObject.abstract.php';


class BaseController extends atDynamicObjectAbstract
{

    public $module;
    public $name;
    public $refName;
    public $version;
    public $build;
    public $copyright;

	public $requiresAuthentication = true;
    public $redirectURL = '';

    public $pagePath;

    public $plugins;


	function __construct()
    {

        parent::__construct();

//        $this->enableLog();

        //---- Unset defined properties before defining virtual versions
        //---- of the properties
        unset($this->module);
        unset($this->name);
        unset($this->refName);
        unset($this->version);
        unset($this->build);
        unset($this->copyright);

        unset($this->requiresAuthentication);
        unset($this->redirectURL);

        unset($this->pagePath);


        //---- Define virtual properties.
        $this->addProperty('module', null);
        $this->addProperty('name', 'Controller');
        $this->addProperty('refName', 'controller');
        $this->addProperty('version', '1.0');
        $this->addProperty('build', '2');
        $this->addProperty('copyright', '(c) 2010-2011');

        $this->addProperty('redirectURL', '');

        $this->addProperty('requiresAuthentication', true);

        $this->addProperty('pagePath', '');


        $this->plugins = new PluginCollection();
        

		return true;
	}


    function init()
    {
        global $phpAnvil;

        $return = true;

        $this->pagePath = $phpAnvil->site->webPath;
        if (isset($_SERVER['REDIRECT_URL'])) {
            $this->pagePath .= ltrim($_SERVER['REDIRECT_URL'], '/');
        }

//        $this->logDebug($this->pagePath, 'pagePath');


//        $phpAnvil->triggerEvent($this->module . '.' . $this->refName . '_controller.init');
        $phpAnvil->triggerEvent('controller.init',
            array('module' => $this->module->name, 'controller' => $this->refName));

        if ($this->requiresAuthentication & !$phpAnvil->userAuthenticated)
        {
            $return = $phpAnvil->application->authenticateUser();
            $phpAnvil->userAuthenticated = $return;

            if (!$phpAnvil->userAuthenticated)
            {
                $this->logVerbose('Setting redirect to login page.');
                $this->redirectURL = $phpAnvil->site->webPath;
                if (!empty($phpAnvil->application->loginModule)) {
                    $this->redirectURL .= $phpAnvil->application->loginModule . '/';
                }
                $this->redirectURL .= $phpAnvil->application->loginAction;
            }
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;

        $return = true;

        if (!empty($this->redirectURL))
        {
            $this->logVerbose('Redirecting...');

            header('Location: ' . $this->redirectURL);
//            exit;
            $return = false;
        } else {
            $phpAnvil->triggerEvent('controller.open',
                array('module' => $this->module->name, 'controller' => $this->refName));
        }

        return $return;
    }


    function close()
    {
        global $phpAnvil;

//        $phpAnvil->triggerEvent($this->module . '.' . $this->refName . '_controller.close');
        $phpAnvil->triggerEvent('controller.close',
            array('module' => $this->module->name, 'controller' => $this->refName));

        return true;
    }


	function loadModules() {
		return true;
	}


	function Process() {
		return true;
	}


    function processGET() {
   		return true;
   	}

    function processPOST() {
   		return true;
   	}

    public function loadPlugin($moduleRefName, $pluginName, $id = 1)
    {
        global $phpAnvil;

        $pluginClassName = $pluginName . 'Plugin';
        $moduleRefName = strtolower($moduleRefName);
        $pluginName = strtolower($pluginName);

        $fullPluginName = $moduleRefName . '.' . $pluginName . '.' . $id;

        $return = true;

        if (!$this->plugins->contains($fullPluginName)) {

            $return = $phpAnvil->loadModule($moduleRefName);

            if ($return) {

                $this->logVerbose('Loading controller plugin (' . $pluginName . ') for Module (' . $moduleRefName . ')...');


                //---- Build File Path to the Controller
                $filePath = 'modules/' . $moduleRefName . '/controllers/plugins/' . $pluginName . '.plugin.php';

                if (file_exists(APP_PATH . $filePath)) {
                    $filePath = APP_PATH . $filePath;
                } else {
                    if (file_exists(PHPANVIL_FRAMEWORK_PATH . $filePath)) {
                        $filePath = PHPANVIL_FRAMEWORK_PATH . $filePath;
                    } else
                    {
                        $this->logError('Controller (' . $pluginName . ') for Module (' . $moduleRefName . ') not found.');
                        $return = false;
                    }
                }

                if ($return) {

                    include_once $filePath;

                    $this->plugins[$fullPluginName] = new $pluginClassName();
                    $this->plugins[$fullPluginName]->id = $id;
                    $this->plugins[$fullPluginName]->controller = $this;
                    $this->plugins[$fullPluginName]->module = $phpAnvil->module[$moduleRefName];

                    $return = $this->plugins[$fullPluginName];
                }
            }
        }

        return $return;
    }


    public function initPlugins()
    {
        for($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->init();
        }
    }

    public function openPlugins()
    {
        for($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->open();
        }
    }

    public function closePlugins()
    {
        for($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->close();
        }
    }
}

?>