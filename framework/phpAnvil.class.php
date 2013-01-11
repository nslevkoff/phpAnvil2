<?php
/**
 * @file
 *               phpAnvil2 Framework Controller
 *
 * @author       Nick Slevkoff <nick@slevkoff.com>
 * @copyright    Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *               This source file is subject to the new BSD license that is
 *               bundled with this package in the file LICENSE.txt. It is also
 *               available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup      phpAnvil2
 */


require_once PHPANVIL2_COMPONENT_PATH . 'anvilAjax.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilSession.class.php';
//require_once PHPANVIL2_COMPONENT_PATH . 'anvilMessage.class.php';
//require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilRegional.class.php';

require_once MOBILE_DETECT_PATH . 'Mobile_Detect.php';


/**
 *
 * Primary processing controller for the phpAnvil framework.
 *
 * @author       Nick Slevkoff <nick@slevkoff.com>
 * @copyright    Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup      phpAnvil
 */
class phpAnvil2 extends anvilObjectAbstract
{

    const VERSION = '2.0';
    const BUILD   = '1';

    const SOURCE_TYPE_UNKNOWN   = 1;
    const SOURCE_TYPE_USER      = 2;
    const SOURCE_TYPE_SYSTEM    = 3;
    const SOURCE_TYPE_AJAX      = 4;
    const SOURCE_TYPE_BP        = 5;
    const SOURCE_TYPE_API       = 6;
    const SOURCE_TYPE_GENERATED = 7;
    const SOURCE_TYPE_EMAIL     = 8;
    const SOURCE_TYPE_IMPORT    = 9;


    public $qsModule = 'anvil_module';
    public $qsAction = 'anvil_action';

    public $hideModuleQS = false;

    public $moduleOverride;
    public $actionOverride;

    /**
     * @var anvilSession
     */
    public $session;

//    public $actionMsg;
//    public $errorMsg;
//    public $pageMsg;

    public $controller = null;

    public $database = null;
//    public $listener;
    public $module = null;
    public $option = null;
    public $path = null;

    /**
     * @var anvilDataConnectionAbstract
     */
    public $db;

    public $ui;

    /**
     * @var anvilSiteAbstract
     */
    public $site;

    /**
     * @var anvilApplicationAbstract
     */
    public $application;
    public $userAuthenticated = false;


    private $_configContent = '';
    private $_eventListeners = array();
    private $_connectedEvents = array();


    /**
     * @var anvilRegional|null
     */
    public $regional = null;
    public $modelDictionary = null;

    public $isNewSession;
    public $isNewUser;
    public $isBot;
    public $sourceTypeID = self::SOURCE_TYPE_USER;
    public $sourceID;

    public $requestPathArray;

    public $isCLI = false;
    public $isConsole = false;

    /** @var Mobile_Detect */
    public $mobileDetect;

    public $logLevelOverride;


    public function __construct()
    {
//        global $options;

        $this->enableLog();

        $this->_core = $this;

        $this->isCLI = PHP_SAPI == 'cli';
        $this->isConsole = $this->isCLI;

//        if ($this->isCLI) {
//            $this->sourceTypeID = self::SOURCE_TYPE_BP;
//        } else {
            $this->sourceTypeID = self::SOURCE_TYPE_USER;
//        }


//        $this->addProperty('isNewSession', false);
//        $this->addProperty('isNewUser', false);
//        $this->addProperty('isBot', false);
//        $this->addProperty('sourceTypeID', 0);
//        $this->addProperty('sourceID', 0);


        $this->controller = new ControllerCollection();


        $this->database = new DatabaseCollection();


        $this->module = new ModuleCollection();

        $this->option = new OptionCollection();


        $this->path = new PathCollection();

        $this->regional = new anvilRegional();

        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Class Constructed.');
//        $this->_logVerbose('Class Constructed.');

//        $requestPath = $_SERVER['SCRIPT_URL'];
//        $this->_logDebug($requestPath, '$requestPath');
//        $this->requestPathArray = explode('/', $requestPath);
    }


    function init()
    {
        $return = false;

        if ($this->isCLI) {
            $this->requestPathArray[0] = SITE_DOMAIN;
            $this->requestPathArray[1] = 'bp';
        } else {
            $this->mobileDetect = new Mobile_Detect();

            $requestPath = $_SERVER['SCRIPT_URL'];
            $this->requestPathArray = explode('/', $requestPath);
        }


//        $this->_logDebug($requestPath, '$requestPath2');
//        $this->_logDebug($this->requestPathArray, '$this->requestPathArray');
        //        $this->webRootPath = str_replace('index.php', '', $_SERVER["SCRIPT_NAME"]);


        //---- Initialize Message Controls
//        $this->actionMsg = new anvilMessage('ActionMsg', 'ActionMsg', 'msg');
        //        $this->actionMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iActionMsg.png';
//        $this->actionMsg->singleBox = false;

//        $this->errorMsg = new anvilMessage('ErrorMsg', 'ErrorMsg', 'msg');
        //        $this->errorMsg->enableTrace();
        //        $this->errorMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iErrorMsg.png';
//        $this->errorMsg->singleBox = false;

//        $this->pageMsg = new anvilMessage('PageMsg', 'PageMsg', 'msg');
        //        $this->pageMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iPageMsg.png';
//        $this->pageMsg->singleBox = false;


        //---- Initialize Session Object
        $this->session = new anvilSession();


        $this->triggerEvent('phpAnvil.init');


        //---- Check if Application is Set
        if (isset($this->application)) {
            //---- Initialize the Application
            $return = $this->application->init();

//            $this->_logDebug('Post application.init...');

            if ($return) {

                if ($this->application->forceSSL && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
                    if (!headers_sent()) {
                        header("Status: 301 Moved Permanently");
                        header(sprintf(
                            'Location: https://%s%s',
                            $_SERVER['HTTP_HOST'],
                            $_SERVER['REQUEST_URI']
                        ));
                        exit();
                    }
                }


                $this->application->requestedModule = !empty($this->moduleOverride)
                        ? $this->moduleOverride
                        : (isset($_GET[$this->qsModule])
                                ? $_GET[$this->qsModule]
                                : $this->application->defaultModule);
                $this->application->requestedAction = !empty($this->actionOverride)
                        ? $this->actionOverride
                        : (isset($_GET[$this->qsAction])
                                ? $_GET[$this->qsAction]
                                : '');

                if ($this->application->requestedModule == 'index.php') {
                    $this->application->requestedModule = $this->application->defaultModule;
                    $this->application->requestedAction = $this->application->defaultAction;
                } elseif ($this->application->requestedAction == 'index.php') {
                    $this->application->requestedAction = '';
                }

            }

        } else {
            //            FB::error('Application not set in phpAnvil.');
            $this->_logError('Application not set in phpAnvil.');
        }


        return $return;
    }


    function open()
    {
        $return = false;

        #--- Set Server App Timezone
        if (version_compare(phpversion(), "5.1.0", ">")) {
            date_default_timezone_set($this->site->timeZone);
        }


        //---- Start Session
        $this->_logVerbose('Starting session...');
        $this->session->dataConnection = $this->db;
        //        $this->session->enableTrace();
        //        $this->session->innactiveTimeout = 60 * 60;
        $this->session->open();

        $this->regional->timezoneOffset = $this->session->timezoneOffset;

        //        FB::log('regional->timezoneOffset set.');
        //        FB::log('test');

        if (!empty($this->regional->timezoneOffset)) {
//            $this->regional->dateTimeZone = new DateTimeZone('Etc/GMT' . $this->regional->timezoneOffset);
            $this->regional->dateTimeZone = new DateTimeZone('UTC' . $this->regional->timezoneOffset);
            //            FB::log('regional->dateTimeZone set.');
        }

        $this->triggerEvent('phpAnvil.open');


        //---- Check if Application is Set
        if (isset($this->application)) {
            //---- Open the Application
            $this->_logVerbose('Opening application...');
            $return = $this->application->open();

        } else {
            //            FB::error('Application not set in phpAnvil.');
            $this->_logError('Application not set in phpAnvil.');
        }

        return $return;
    }


    function addConfigContent($content)
    {

        //        FB::log($content);

        $this->_configContent .= $content . "\n";

        return true;
    }


    public function buildConfigFile()
    {
    }


    function connectEventListener($eventListener)
    {
        $this->_eventListeners[]                       = $eventListener;
        $this->_connectedEvents[$eventListener->event] = true;
        return true;
    }


    public function valueOr($value, $orValue)
    {
        if (empty($value)) {
            return $orValue;
        } else {
            return $value;
        }
    }


    public function getActionID($moduleCode, $actionConstant)
    {
        global $moduleIDs, $actions;

        $return     = -1;
        $moduleCode = strtolower($moduleCode);

        if (isset($actions[$moduleCode][$actionConstant])) {
            $return = $actions[$moduleCode][$actionConstant];
        } else {
            if (isset($actions['*'][$actionConstant])) {
                $return = $actions['*'][$actionConstant];
            }
        }

        return $return;
    }


    public function getModuleCode($module)
    {
        global $moduleCodes, $moduleIDs;

        //        FB::log($module);
        //        FB::log($moduleIDs);

        $return = $module;

        if (is_numeric($module)) {
            $return = $moduleCodes[$module];
        } elseif (array_key_exists(strtolower($module), $moduleIDs)) {
            $return = $moduleCodes[$moduleIDs[strtolower($module)]];
        }

        return $return;
    }


    public function loadController($moduleRefName, $controllerName = '')
    {
        global $phpAnvil;

        $moduleRefName  = strtolower($moduleRefName);
        $controllerName = strtolower($controllerName);

        $return = true;

        if (empty($controllerName) || $controllerName == '*') {

            $return = $this->loadModule($moduleRefName);
            //            $controllerClassName = $this->module[$moduleRefName]->defaultController . 'Controller';
            $controllerName = strtolower($this->module[$moduleRefName]->defaultController);
        }

        $controllerClassName = $controllerName . 'Controller';

        if (!$this->controller->contains($moduleRefName . '.' . $controllerName)) {

            $return = $this->loadModule($moduleRefName);

            if ($return) {

                //                $msg = 'Loading controller (' . $controllerName . ') for Module (' . $moduleRefName . ')...';
                //                $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
                //                FB::info($msg);
                $this->_logVerbose('Loading controller (' . $controllerName . ') for Module (' . $moduleRefName . ')...');


                //---- Build File Path to the Controller
                $filePath = 'modules/' . $moduleRefName . '/controllers/' . $controllerName . '.controller.php';

                if (file_exists(APP_PATH . $filePath)) {
                    $filePath = APP_PATH . $filePath;
                } else {
                    if (file_exists(PHPANVIL2_FRAMEWORK_PATH . $filePath)) {
                        $filePath = PHPANVIL2_FRAMEWORK_PATH . $filePath;
                    } else {
                        if (isset($this->application->catchAllAction) && $this->application->catchAllAction != $controllerName) {
                            $this->loadController($this->application->catchAllModule, $this->application->catchAllAction);
                        } else {
                            $this->_logError('Controller (' . $controllerName . ') for Module (' . $moduleRefName . ') not found.');
                        }
                        //                    FB::error('Controller (' . $controllerName . ') for Module (' . $moduleRefName . ') not found.');
                        $return = false;
                    }
                }

                if ($return) {
                    //                FB::log($filePath, '$filePath');

                    include_once $filePath;


                    $fullControllerName = $moduleRefName . '.' . $controllerName;

                    if (!$this->controller->contains($fullControllerName)) {
                        //                        $this->_logDebug($fullControllerName, '$fullControllerName');
                        //                        $this->_logDebug($controllerClassName, '$controllerClassName');

                        $this->controller[$fullControllerName] = new $controllerClassName();
                    }

                    $controller = $this->controller[$fullControllerName];
                    /** @var $controller anvilControllerAbstract */

//                    $this->controller[$fullControllerName]->module  = $this->module[$moduleRefName];
//                    $this->controller[$fullControllerName]->refName = $controllerName;
                    $controller->module = $this->module[$moduleRefName];
                    $controller->refName = $controllerName;

//                    $return = $this->controller[$fullControllerName]->init();
                    $return = $controller->init();
                    if ($return) {
                        //                        if (!empty($_GET)) {
//                        $return = $this->controller[$fullControllerName]->processGET();
                        $return = $controller->processGET();
                        //                        }
                        if ($return && !empty($_POST)) {
//                            $return = $this->controller[$fullControllerName]->processPOST();
                            $return = $controller->processPOST();
                        }
//                        if ($return && empty($this->controller[$fullControllerName]->redirectURL)) {
                        if ($return && empty($controller->redirectURL)) {
//                                $return = $this->controller[$fullControllerName]->open();
                            $return = $controller->open();
                            if (!$return) {
                                //                            FB::warn('Controller (' . $fullControllerName . ') failed to open.');
                                $this->_logWarning('Controller (' . $fullControllerName . ') failed to open.');
                                //                            header('Location: ' . $phpAnvil->controller[$fullControllerName]->redirectURL);
                            }
                        }

                        $this->_logVerbose('Closing controller...');

//                        $return = $this->controller[$fullControllerName]->close();
                        $return = $controller->close();

                    }

//                    if (!empty($this->controller[$fullControllerName]->redirectURL)) {
                    if (!empty($controller->redirectURL)) {
                            $this->_logVerbose('Redirecting...');

//                        $redirectURL = $this->controller[$fullControllerName]->redirectURL;
                        $redirectURL = $controller->redirectURL;
                        if (substr($redirectURL, 0, 4) != 'http') {
                            $redirectURL = $this->site->webPath . $redirectURL;
                        }

                        header('Location: ' . $redirectURL);

                    } else {
                        //                        FB::warn('Controller (' . $fullControllerName . ') failed to init.');
                        //                        $this->_logWarning('Controller (' . $fullControllerName . ') failed to init.');
                        //                        header('Location: ' . $phpAnvil->controller[$fullControllerName]->redirectURL);
                    }

                }
            }
        }

        return $return;
    }


    public function loadModule($moduleRefName)
    {
        global $phpAnvil;

        //        FB::log($moduleRefName, '$moduleRefName');

        if (empty($moduleRefName)) {
            $moduleRefName = $this->application->defaultModule;

            if (empty($moduleRefName)) {
                $this->_logError('Unable to load the module, both the passed reference name and default module name are undefined.');
            }
        }

        $return = !empty($moduleRefName);
        //        $return = true;

        $moduleRefName = strtolower($moduleRefName);


        if ($return && !$this->module->contains($moduleRefName)) {
            //        if (!$this->module->contains($moduleRefName)) {

            //            $msg = 'Loading Module (' . $moduleRefName . ')...';
            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_DEBUG);
            //            FB::info($msg);
            $this->_logVerbose('Loading Module (' . $moduleRefName . ')...');

            $filePath = 'modules/' . $moduleRefName . '/' . $moduleRefName . '.module.php';


            //            FB::log($filePath, '$filePath');

            if (file_exists(APP_PATH . $filePath)) {
                $filePath = APP_PATH . $filePath;
            } else {
                if (file_exists(PHPANVIL2_FRAMEWORK_PATH . $filePath)) {
                    $filePath = PHPANVIL2_FRAMEWORK_PATH . $filePath;
                } else {
                    //                FB::error('Module (' . $moduleRefName . ') not found.  Please check module reference name and that the module has been installed on the server.');
                    $this->_logError('Module (' . $moduleRefName . ') not found.  Please check module reference name and that the module has been installed on the server.');
                    $return = false;
                }
            }

            //            $this->_logDebug($filePath, '$filePath');


            if ($return) {
                //                FB::log($filePath, '$filePath');

                include_once $filePath;

                $return = $this->module[$moduleRefName]->init();

                if ($return) {
                    //                    $return = $this->module[$moduleRefName]->open();
                    //                    if (!$return)
                    //                    {
                    //                        FB::warn('Module (' . $moduleRefName . ') failed to open.');
                    //                    }
                } else {
                    //                    FB::warn('Module (' . $moduleRefName . ') failed to init.');
                    $this->_logWarning('Module (' . $moduleRefName . ') failed to init.');
                }

            }
        }

        return $return;
    }


    public function loadAllCustomModules()
    {
        //---- Get Custom Module Directories
        $moduleDirectories = glob(APP_PATH . 'modules/*', GLOB_ONLYDIR);

        //        FB::log('Loading all custom modules...');
        $this->_logVerbose('Loading all custom modules...');

        $count = count($moduleDirectories);
        for ($i = 0; $i < $count; $i++) {
            //            FB::log(basename($moduleDirectories[$i]), 'Module Directory');
            $this->loadModule(basename($moduleDirectories[$i]));
        }
    }


    public function installModule($moduleRefName)
    {
    }


    public function openModules()
    {
        $moduleKeys = $this->module->keys();
        $max        = count($moduleKeys);
        for ($i = 0; $i < $max; $i++) {
            $this->module[$moduleKeys[$i]]->open();
        }
    }


    public function prepareNewAction($source, $module, $type, $data)
    {

        $action         = new Action();
        $action->source = $source;
        $action->module = $module;
        $action->type   = $type;
        $action->data   = $data;

        return $action;
    }


    public function processBP($controllerName = '', $batch = null, $task = null)
    {
        global $phpAnvil;

        $return = true;

        $controllerName = strtolower($controllerName);

        $controllerClassName = $controllerName . 'BP';

        if (!$this->controller->contains('bp.' . $controllerName)) {

            $this->_logVerbose('Loading BP controller (' . $controllerName . ')...');


            //---- Build File Path to the Controller
            $filePath = 'bp/' . $controllerName . '.bp.php';
            $filePath = APP_PATH . $filePath;

            if (file_exists($filePath)) {
            } else {
                $this->_logError('BP Controller (' . $controllerName . ') not found in path: ' . $filePath);
                $return = false;
            }

            if ($return) {
                include_once $filePath;

                $fullControllerName = 'bp.' . $controllerName;

                if (!$this->controller->contains($fullControllerName)) {
                    if ($batch) {
                        if ($task) {
                            $this->controller[$fullControllerName] = new $controllerClassName($batch, $task);
                        } else {
                            $this->controller[$fullControllerName] = new $controllerClassName($batch);
                        }
                    } else {
                        $this->controller[$fullControllerName] = new $controllerClassName();
                    }
                }

//                    $this->controller[$fullControllerName]->module  = '';
//                    $this->controller[$fullControllerName]->refName = $controllerName;

                $return = $this->controller[$fullControllerName]->init();
                if ($return) {
                    $return = $this->controller[$fullControllerName]->process();
                    $return = $this->controller[$fullControllerName]->close();
                }
            }
        }

        return $return;
    }


    public function processNewAction($source, $module, $type, $data)
    {
        return $this->processAction($this->prepareNewAction($source, $module, $type, $data));
    }


    public function processModuleAction(Action $action, $module)
    {
        $action->module = $module;
        return $this->processAction($action);
    }


    public function processAction(Action $action)
    {
        $return = true;

        $moduleRefName = $action->module;

        if ($moduleRefName != '*') {
            $this->loadModule($moduleRefName);
            $return = $this->modules[$moduleRefName]->processAction($action);

        } else {
            $max = $this->module->count();

            for ($i = 0; $i < $max; $i++) {
                $return = $this->module[$i]->processAction($action);
            }

        }

        return $return;
    }


    public function processAjaxAction()
    {

        $objAjax    = new anvilAjax();
        $ajaxPacket = $_POST['request_packet'];

        $ajaxPacket    = str_replace('\"', '"', $ajaxPacket);
        $requestPacket = json_decode($ajaxPacket);

        foreach ($requestPacket as $ajaxRequest) {

            $moduleID       = $ajaxRequest->moduleID;
            $moduleCode     = $this->getModuleCode($moduleID);
            $moduleActionID = $ajaxRequest->moduleActionID;

            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Processing Ajax Action (' . $moduleActionID . ') for Module (' . $moduleCode . ')', self::TRACE_TYPE_DEBUG);
            $this->_logVerbose('Processing Ajax Action (' . $moduleActionID . ') for Module (' . $moduleCode . ')');

            $data['sourceID']         = $ajaxRequest->sourceID;
            $data['responseTargetID'] = $ajaxRequest->responseTargetID;
            $data['responseActionID'] = $ajaxRequest->responseActionID;

            $data['data'] = $ajaxRequest->data;

            $action = $this->prepareNewAction(SOURCE_TYPE_AJAX, $moduleID, $moduleActionID, $data);

            $return = $this->processAction($action);

            if (!empty($return)) {
                $objAjax->addResponse($ajaxRequest->sourceID, $ajaxRequest->responseActionID, $ajaxRequest->responseTargetID, $return);
            }
        }

        $objAjax->send();

        return true;
    }


    public function processWebAction($moduleRefName, $webAction)
    {
    }


    public function loadWidget($moduleRefName, $widget)
    {
        //		global $modules, $moduleIDs, $moduleTypes;

        $return = false;

        $moduleRefName = strtolower($moduleRefName);
        $widget        = strtolower($widget);
        //		$moduleCode = $this->getModuleCode($module);
        //		$moduleID = $moduleIDs[strtolower($moduleCode)];

        if ($this->loadModule($moduleRefName)) {
            //		if (is_array($moduleIDs) && array_key_exists(strtolower($moduleCode), $moduleIDs)) {
            //            $msg = 'Loading Widget (' . $moduleRefName . "." . $widget . ')...';
            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
            //            FB::info($msg);
            $this->_logVerbose('Loading Widget (' . $moduleRefName . "." . $widget . ')...');


            $filePath = 'modules/' . $moduleRefName . '/widgets/' . $widget . '.widget.php';

            //            FB::log($filePath, '$filePath');

            if ($this->module[$moduleRefName]->type === BaseModule::TYPE_CUSTOM) {
                $filePath = APP_PATH . $filePath;
            } else {
                $filePath = PHPANVIL2_FRAMEWORK_PATH . $filePath;
            }

            //            FB::log($filePath, '$filePath');

            include_once $filePath;

            $return = true;

            //		} else {

            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, "Module '" . $moduleCode . "' not installed.", self::TRACE_TYPE_DEBUG);
        }

        return $return;

    }


    public function loadDictionary($dictionary)
    {
        //		global $modules, $moduleIDs, $moduleTypes;
        //		global $options, $phrases;
        global $phrases;

        $dictionary = strtolower($dictionary);

        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, "Loading Dictionary '" . $dictionary . "'...", self::TRACE_TYPE_DEBUG);

        $filePath = APP_PATH . 'lang/' . 'en/' . $dictionary . '.dictionary.php';

        return require_once($filePath);
    }


    public function execute()
    {
        //        FB::group('Initializing...');
        $this->_logGroup('Initializing... [M: ' . $this->formatBytes(memory_get_usage(true)) . ']');

        if ($this->init()) {
            $this->_logGroupEnd();
            //            FB::groupEnd();
            //            FB::group('Opening...');

//            $this->_logDebug('Opening...');

            $this->_logGroup('Opening... [M: ' . $this->formatBytes(memory_get_usage(true)) . ']');

            $this->open();

            $this->_logGroupEnd();
            //            FB::groupEnd();
            //            FB::group('Closing...');
            $this->_logGroup('Closing... [M: ' . $this->formatBytes(memory_get_usage(true)) . ']');

            $this->close();
        } else {
            $this->_logError('Initializing failed... [M: ' . $this->formatBytes(memory_get_usage(true)) . ']');
        }
        $this->_logGroupEnd();
        //        FB::groupEnd();
        $this->_logInfo('END OF LINE. [pM: ' . $this->formatBytes(memory_get_peak_usage(true)) . ']');
        //        FB::info('END OF LINE.');
    }


    public function close()
    {

        //---- Check if Application is Set
        if (isset($this->application)) {
            //---- Open the Application
            $this->application->close();

        } else {
            //            FB::error('Application not set in phpAnvil.');
            $this->_logError('Application not set in phpAnvil.');
        }


        $this->triggerEvent('phpAnvil.close');


        $this->session->close();

        //        $this->_logDebug('session closed');

        sendDebugTrace();
        //		$this->db->close();

    }


    public function triggerEvent($event, $parameters = '')
    {
        $return = false;

//        if (array_key_exists($event, $this->_connectedEvents)) {
        if ($return && array_key_exists($event, $this->_connectedEvents)) {

                //            $msg = 'Triggering event (' . $event . ')...';
            //            $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
            //            FB::info($msg);
            $this->_logVerbose('Triggering event (' . $event . ')...');

            //---- Loop through All Connected Event Listeners for Matches
            $max = count($this->_eventListeners);
            for ($i = 0; $i < $max; $i++) {
                //---- Check if Event Listener Matches the Triggered Event
                if ($this->_eventListeners[$i]->event === $event) {
                    //---- Is callback valid?
                    if (is_callable($this->_eventListeners[$i]->callback)) {
                        //---- Yes - Call the callback.
                        if (is_array($parameters)) {
                            $return = call_user_func_array($this->_eventListeners[$i]->callback, $parameters);
                        } else {
                            $return = call_user_func($this->_eventListeners[$i]->callback, $parameters);
                        }
                    } else {
                        //---- No - Report Warning
                        //                        FB::warn('Invalid Event Listener (' . $this->_eventListeners[$i]->callback . ')!');
                        $this->_logWarning('Invalid Event Listener (' . $this->_eventListeners[$i]->callback . ')!');
                    }
                }
            }
        }

        return $return;
    }


    public function uninstallModule($moduleRefName)
    {
        global $installers, $moduleIDs, $moduleTypes;
        global $options;

        $return = true;

        $module = strtolower($moduleRefName);

        $moduleCode = $this->getModuleCode($module);
        $msg        = "Uninstalling Module '" . $moduleCode . "'...";

        $newModule = new ModuleModel($this->db);

        if ($newModule->loadCode($moduleCode)) {
            //            $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_DEBUG);
            //            $this->_logVerbose($msg);

            //---- Determine Module Type
            $filePath = 'modules/' . $moduleCode . '/';
            if ($moduleTypes[$newModule->id] == MODULE_TYPE_CUSTOM) {
                $filePath = APP_PATH . $filePath;
            } else {
                $filePath = PHPANVIL2_FRAMEWORK_PATH . $filePath;
            }

            if (file_exists($filePath)) {
                $filePath .= $moduleCode . '.install.php';

                //                FB::log($filePath);

                include_once($filePath);

                $installers[$moduleCode]->uninstall($newModule);
            } else {
                $msg = 'Module (' . $moduleCode . ') does not exist.';
                //                $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_ERROR);
                $return = false;
            }
        } else {
            $msg    = 'Module (' . $moduleCode . ') is not installed.';
            $return = false;
        }
        //        FB::log($msg);
        $this->_logVerbose($msg);


        return $return;

    }


    public function convertDTSAgo($fromDTS, $toDTS = -1)
    {
        $return = '';

        if ($fromDTS == 0) {
            $return = 'A long time ago';
        } else {

            if ($toDTS == -1) {
                $toDTS = time();
            }

            // Make the entered date into Unix timestamp from MySQL datetime field

            $fromDTS = strtotime($fromDTS);

            // Calculate the difference in seconds betweeen
            // the two timestamps

            $difference = $toDTS - $fromDTS;


            //            fb::log(12 * 30 * 24 * 60 * 60, 'year');
            //            fb::log(30 * 24 * 60 * 60, 'month');
            //            fb::log(24 * 60 * 60, 'day');
            //            fb::log(60 * 60, 'hour');
            //            fb::log(60, 'minute');


            switch (true) {
                // If difference is less than 60 seconds,
                // seconds is a good interval of choice
                case(strtotime('-1 min', $toDTS) < $fromDTS):
                    $datediff = $difference;
                    $return   = ($datediff == 1)
                            ? $datediff . ' second ago'
                            : $datediff . ' seconds ago';
                    break;
                // If difference is between 60 seconds and
                // 60 minutes, minutes is a good interval
                case(strtotime('-1 hour', $toDTS) < $fromDTS):
                    $datediff = floor($difference / 60);
                    $return   = ($datediff == 1)
                            ? $datediff . ' minute ago'
                            : $datediff . ' minutes ago';
                    break;
                // If difference is between 1 hour and 24 hours
                // hours is a good interval
                case(strtotime('-1 day', $toDTS) < $fromDTS):
                    $datediff = floor($difference / 60 / 60);
                    $return   = ($datediff == 1)
                            ? $datediff . ' hour ago'
                            : $datediff . ' hours ago';
                    break;
                // If difference is between 1 day and 7 days
                // days is a good interval
                case(strtotime('-1 week', $toDTS) < $fromDTS):
                    $day_difference = 1;
                    while (strtotime('-' . $day_difference . ' day', $toDTS) >= $fromDTS) {
                        $day_difference++;
                    }

                    $datediff = $day_difference;
                    $return   = ($datediff == 1)
                            ? 'yesterday'
                            : $datediff . ' days ago';
                    break;
                // If difference is between 1 week and 30 days
                // weeks is a good interval
                case(strtotime('-1 month', $toDTS) < $fromDTS):
                    $week_difference = 1;
                    while (strtotime('-' . $week_difference . ' week', $toDTS) >= $fromDTS) {
                        $week_difference++;
                    }

                    $datediff = $week_difference;
                    $return   = ($datediff == 1)
                            ? 'last week'
                            : $datediff . ' weeks ago';
                    break;
                // If difference is between 30 days and 365 days
                // months is a good interval, again, the same thing
                // applies, if the 29th February happens to exist
                // between your 2 dates, the function will return
                // the 'incorrect' value for a day
                case(strtotime('-1 year', $toDTS) < $fromDTS):
                    $months_difference = 1;
                    while (strtotime('-' . $months_difference . ' month', $toDTS) >= $fromDTS) {
                        $months_difference++;
                    }

                    $datediff = $months_difference;
                    $return   = ($datediff == 1)
                            ? $datediff . ' month ago'
                            : $datediff . ' months ago';

                    break;
                // If difference is greater than or equal to 365
                // days, return year. This will be incorrect if
                // for example, you call the function on the 28th April
                // 2008 passing in 29th April 2007. It will return
                // 1 year ago when in actual fact (yawn!) not quite
                // a year has gone by
                case(strtotime('-1 year', $toDTS) >= $fromDTS):
                    $year_difference = 1;
                    while (strtotime('-' . $year_difference . ' year', $toDTS) >= $fromDTS) {
                        $year_difference++;
                    }

                    $datediff = $year_difference;
                    $return   = ($datediff == 1)
                            ? $datediff . ' year ago'
                            : $datediff . ' years ago';
                    break;
            }
        }

        return $return;
    }


    public function convertDaysForDisplay($days)
    {
        $return = '';

        if ($days == 0) {
            $return = '';
        } else {

            //            fb::log($days, '$days');

            //            $date = date_create(gmdate('Y-m-d', strtotime('+' . $days . ' day')));
            $date = date_create($days + 1 . ' days');

            //            fb::log($date, '$date');

            $return = $this->ago($date);

            //            if ($days == 1)
            //            {
            //                $return = '1 day';
            //            } elseif ($days < 30)
            //            {
            //                $return = $days . ' days';
            //            } elseif ($days == 30)
            //            {
            //                $return = '1 month';
            //            } elseif ($days < 365)
            //                    break;
            // If difference is between 60 seconds and
            // 60 minutes, minutes is a good interval
            //                case(strtotime('-1 hour', $toDTS) < $fromDTS):
            //                    $datediff = floor($difference / 60);
            //                    $return = ($datediff==1) ? $datediff.' minute ago' : $datediff.' minutes ago';
            //                    break;
            // If difference is between 1 hour and 24 hours
            // hours is a good interval
            //                case(strtotime('-1 day', $toDTS) < $fromDTS):
            //                    $datediff = floor($difference / 60 / 60);
            //                    $return = ($datediff==1) ? $datediff.' hour ago' : $datediff.' hours ago';
            //                    break;
            // If difference is between 1 day and 7 days
            // days is a good interval
            //                case(strtotime('-1 week', $toDTS) < $fromDTS):
            //                    $day_difference = 1;
            //                    while (strtotime('-'.$day_difference.' day', $toDTS) >= $fromDTS)
            //                    {
            //                        $day_difference++;
            //                    }

            //                    $datediff = $day_difference;
            //                    $return = ($datediff==1) ? 'yesterday' : $datediff.' days ago';
            //                    break;
            // If difference is between 1 week and 30 days
            // weeks is a good interval
            //                case(strtotime('-1 month', $toDTS) < $fromDTS):
            //                    $week_difference = 1;
            //                    while (strtotime('-'.$week_difference.' week', $toDTS) >= $fromDTS)
            //                    {
            //                        $week_difference++;
            //                    }

            //                    $datediff = $week_difference;
            //                    $return = ($datediff==1) ? 'last week' : $datediff.' weeks ago';
            //                    break;
            // If difference is between 30 days and 365 days
            // months is a good interval, again, the same thing
            // applies, if the 29th February happens to exist
            // between your 2 dates, the function will return
            // the 'incorrect' value for a day
            //                case(strtotime('-1 year', $toDTS) < $fromDTS):
            //                    $months_difference = 1;
            //                    while (strtotime('-'.$months_difference.' month', $toDTS) >= $fromDTS)
            //                    {
            //                        $months_difference++;
            //                    }

            //                    $datediff = $months_difference;
            //                    $return = ($datediff==1) ? $datediff.' month ago' : $datediff.' months ago';

            //                    break;
            // If difference is greater than or equal to 365
            // days, return year. This will be incorrect if
            // for example, you call the function on the 28th April
            // 2008 passing in 29th April 2007. It will return
            // 1 year ago when in actual fact (yawn!) not quite
            // a year has gone by
            //                case(strtotime('-1 year', $toDTS) >= $fromDTS):
            //                    $year_difference = 1;
            //                    while (strtotime('-'.$year_difference.' year', $toDTS) >= $fromDTS)
            //                    {
            //                        $year_difference++;
            //                    }

            //                    $datediff = $year_difference;
            //                    $return = ($datediff==1) ? $datediff.' year ago' : $datediff.' years ago';
            //                    break;
            //            }
        }

        return $return;
    }


    public function pluralize($count, $text)
    {
        return $count . (($count == 1)
                ? (" $text")
                : (" ${text}s"));
    }


    public function ago($time, $abbreviate = false)
    {
        if (!is_numeric($time)) {
            date_default_timezone_set($this->regional->dateTimeZone->getName());
            $time = strtotime($time);
            date_default_timezone_set('UTC');
        }

        $appendText = '';
        $difference = time() - $time;

        if ($abbreviate) {
            $return          = 'now';

            $timeNameArray          = array('Y'   => 31556926,
                                'M'  => 2629744,
                                'W'   => 604800,
                                'D'    => 86400,
                                'h'   => 3600,
                                'm' => 60);
        } else {
            $return = 'just now';

            $timeNameArray = array(' year'   => 31556926,
                                   ' month'  => 2629744,
                                   ' week'   => 604800,
                                   ' day'    => 86400,
                                   ' hour'   => 3600,
                                   ' minute' => 60,
                                   ' second' => 1);

            if ($difference >= 0) {
                $appendText = ' ago';
            }
        }

        foreach ($timeNameArray as $timeName => $timeSeconds) {
            if ($timeSeconds <= $difference) {

                $timeDisplayAmount = floor($difference / $timeSeconds);

                $return = "$timeDisplayAmount$timeName" . ($timeDisplayAmount == 1
                        ? ''
                        : (!$abbreviate ? 's'
                        : '')) . $appendText;
                break;
            }
        }

        return $return;
    }


    public function getDate($date = '', $timeZone = '', $format = '')
    {
        if (empty($date)) {
            //            $date = date('c');
            //            $date = new DateTime(null, $this->regional->dateTimeZone);
            $date = null;
        }


        if (!empty($timeZone)) {
            $value = new DateTime($date, new DateTimeZone($timeZone));
        } elseif (isset($this->regional->dateTimeZone)) {
            $value = new DateTime($date, $this->regional->dateTimeZone);
        } else {
            $value = new DateTime($date, new DateTimeZone('PST'));
        }

        if (empty($format)) {
            $return = $value->format($this->regional->dateFormat);
        } else {
            $return = $value->format($format);
        }

        return $return;

    }


    public function getDTS($dts = '', $fromTimeZone = '', $format = '', $toTimeZone = '')
    {
        if (empty($dts)) {
            //            $dts = date('c');
            //            $dts = new DateTime(null, $this->regional->dateTimeZone);
            $dts = null;
        }

        if (!empty($fromTimeZone)) {
            $value = new DateTime($dts, new DateTimeZone($fromTimeZone));

            if (!empty($toTimeZone)) {
                $value->setTimezone(new DateTimeZone($toTimeZone));
            } elseif (isset($this->regional->dateTimeZone)) {
                $value->setTimezone($this->regional->dateTimeZone);
            }
        } elseif (isset($this->regional->dateTimeZone)) {
            $value = new DateTime($dts, $this->regional->dateTimeZone);
        } else {
            $value = new DateTime($dts, new DateTimeZone('PST'));
        }

        if (empty($format)) {
            $return = $value->format($this->regional->dtsFormat);
        } else {
            $return = $value->format($format);
        }


        return $return;

    }


    public function encrypt($value, $key = '')
    {
        if (empty($key)) {
            $key = $this->application->cryptKey;
        }
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $value, MCRYPT_MODE_CBC, md5(md5($key))));
    }


    public function decrypt($value, $key = '')
    {
        if (empty($key)) {
            $key = $this->application->cryptKey;
        }
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    }


    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes
                ? log($bytes)
                : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
//        $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }


    public function generateToken($length = 8, $numbersOnly = false)
    {
        if ($numbersOnly) {
            $charset = "1234567890";
        } else {
            $charset = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        $maxLen = strlen($charset) - 1;

        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $charset[rand(0, $maxLen)];
        }

        return $token;
    }


    public function hash($value)
    {
        return md5(utf8_encode($value));
    }
}

?>
