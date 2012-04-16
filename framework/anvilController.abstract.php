<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';
//require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.abstract.php';


abstract class anvilControllerAbstract extends anvilObjectAbstract
{

    /**
     * @var phpAnvil2
     */
    protected $_core;

    /**
     * @var anvilApplicationAbstract
     */
    protected $_application;

    /**
     * @var anvilSiteAbstract
     */
    protected $_site;

    /**
     * @var anvilModuleAbstract
     */
    public $module;

    public $name;
    public $refName;
    public $version = '1.0';
    public $build = '1';
    public $copyright = '(c) 2012';

    public $requiresAuthentication = true;
    public $redirectURL = '';
//    public $webPath;
    public $plugins;

    protected $_pagePath;
    protected $_webPath;

    public $response;


//    public $alerts;

    function __construct()
    {
        global $phpAnvil;

        parent::__construct();

        $this->enableLog();

        $this->_core = $phpAnvil;
        $this->_application = $phpAnvil->application;
        $this->_site = $phpAnvil->site;

        $this->plugins = new PluginCollection();
        //        $this->alerts = new anvilContainer();


        return true;
    }


    function init()
    {
        global $phpAnvil;

        $return = true;

        $this->_webPath = $phpAnvil->site->webPath;
        $this->_pagePath = $this->_webPath;
        if (isset($_SERVER['REDIRECT_SCRIPT_URL'])) {
//            $this->_logDebug($_SERVER['REDIRECT_SCRIPT_URL'], '$_SERVER[REDIRECT_SCRIPT_URL]');

//            phpInfo();

            $this->_pagePath .= ltrim($_SERVER['REDIRECT_SCRIPT_URL'], '/');
        }


//        $this->_logDebug($this->webPath, 'webPath');


        //        $phpAnvil->triggerEvent($this->module . '.' . $this->refName . '_controller.init');
        $phpAnvil->triggerEvent('controller.init',
            array('module'     => $this->module->name,
                  'controller' => $this->refName));

        if ($this->requiresAuthentication & !$phpAnvil->userAuthenticated) {

            $return = $phpAnvil->application->authenticateUser();

            $phpAnvil->userAuthenticated = $return;

            if (!$phpAnvil->userAuthenticated) {
                $this->_authFailed();

                $this->_logVerbose('Setting redirect to login page.');
                $this->redirectURL = $this->_application->loginURL;
            }
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;

        $return = true;

        if (!empty($this->redirectURL)) {
            $this->_logVerbose('Redirecting...');

            header('Location: ' . $this->redirectURL);
            //            exit;
            $return = false;
        } else {
            $phpAnvil->triggerEvent('controller.open',
                array('module'     => $this->module->name,
                      'controller' => $this->refName));
        }

        return $return;
    }


    function close()
    {
        global $phpAnvil;

        //        $phpAnvil->triggerEvent($this->module . '.' . $this->refName . '_controller.close');
        $phpAnvil->triggerEvent('controller.close',
            array('module'     => $this->module->name,
                  'controller' => $this->refName));

        return true;
    }


    protected function _authFailed()
    {
    }


    public function addErrorAlert($title = '', $message = '')
    {
        //        $this->response->alerts->addControl(new anvilAlert('', anvilAlert::TYPE_ERROR, $title, $message));
        $newAlertMessage = '|-[' . $title . ']|' . $message;

        if (array_key_exists('alert.error', $_SESSION)) {
            $_SESSION['alert.error'] .= $newAlertMessage;
        } else {
            $_SESSION['alert.error'] = $newAlertMessage;
        }
    }


    public function addInfoAlert($title = '', $message = '')
    {
        //        $this->response->alerts->addControl(new anvilAlert('', anvilAlert::TYPE_INFO, $title, $message));
        $newAlertMessage = '|-[' . $title . ']|' . $message;

        if (array_key_exists('alert.info', $_SESSION)) {
            $_SESSION['alert.info'] .= $newAlertMessage;
        } else {
            $_SESSION['alert.info'] = $newAlertMessage;
        }
    }


    public function addSuccessAlert($title = '', $message = '')
    {
        //        $this->response->alerts->addControl(new anvilAlert('', anvilAlert::TYPE_SUCCESS, $title, $message));
        $newAlertMessage = '|-[' . $title . ']|' . $message;

        if (array_key_exists('alert.success', $_SESSION)) {
            $_SESSION['alert.success'] .= $newAlertMessage;
        } else {
            $_SESSION['alert.success'] = $newAlertMessage;
        }
    }


    public function addWarningAlert($title = '', $message = '')
    {
        //        $this->response->alerts->addControl(new anvilAlert('', anvilAlert::TYPE_WARNING, $title, $message));
        $newAlertMessage = '|-[' . $title . ']|' . $message;

        if (array_key_exists('alert.warning', $_SESSION)) {
            $_SESSION['alert.warning'] .= $newAlertMessage;
        } else {
            $_SESSION['alert.warning'] = $newAlertMessage;
        }
    }


    function loadModules()
    {
        return true;
    }


    function Process()
    {
        return true;
    }


    function processGET()
    {
        return true;
    }


    function processPOST()
    {
        return true;
    }


    public function loadPlugin($moduleRefName, $pluginName, $id = 1)
    {
        global $phpAnvil;

        $pluginClassName = $pluginName . 'Plugin';
        $moduleRefName   = strtolower($moduleRefName);
        $pluginName      = strtolower($pluginName);

        $fullPluginName = $moduleRefName . '.' . $pluginName . '.' . $id;

        $return = true;

        if (!$this->plugins->contains($fullPluginName)) {

            $return = $phpAnvil->loadModule($moduleRefName);

            if ($return) {

                $this->_logVerbose('Loading controller plugin (' . $pluginName . ') for Module (' . $moduleRefName . ')...');


                //---- Build File Path to the Controller
                $filePath = 'modules/' . $moduleRefName . '/controllers/plugins/' . $pluginName . '.plugin.php';

                if (file_exists(APP_PATH . $filePath)) {
                    $filePath = APP_PATH . $filePath;
                } else {
                    if (file_exists(PHPANVIL2_FRAMEWORK_PATH . $filePath)) {
                        $filePath = PHPANVIL2_FRAMEWORK_PATH . $filePath;
                    } else
                    {
                        $this->_logError('Controller (' . $pluginName . ') for Module (' . $moduleRefName . ') not found.');
                        $return = false;
                    }
                }

                if ($return) {

                    include_once $filePath;

                    $this->plugins[$fullPluginName]             = new $pluginClassName();
                    $this->plugins[$fullPluginName]->id         = $id;
                    $this->plugins[$fullPluginName]->controller = $this;
                    $this->plugins[$fullPluginName]->module     = $phpAnvil->module[$moduleRefName];

                    $return = $this->plugins[$fullPluginName];
                }
            }
        }

        return $return;
    }


    public function initPlugins()
    {
        for ($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->init();
        }
    }


    public function openPlugins()
    {
        for ($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->open();
        }
    }


    public function closePlugins()
    {
        for ($this->plugins->moveFirst(); $this->plugins->hasMore(); $this->plugins->moveNext()) {
            $objPlugin = $this->plugins->current();
            $objPlugin->close();
        }
    }
}

?>