<?php
/**
 * @file
 * phpAnvil Framework Controller
 *
 * @author       Nick Slevkoff <nick@slevkoff.com>
 * @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *     This source file is subject to the new BSD license that is
 *     bundled with this package in the file LICENSE.txt. It is also
 *     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup      phpAnvil
 */


require_once('Controller.collection.php');
require_once('Plugin.collection.php');
require_once('Database.collection.php');
require_once('Module.collection.php');
require_once('Option.collection.php');
require_once('Path.collection.php');

require_once('EventListener.class.php');

require_once('Base.module.php');

require_once PHPANVIL_TOOLS_PATH . 'atAjax.class.php';
require_once PHPANVIL_TOOLS_PATH . 'atSession.class.php';
require_once PHPANVIL_TOOLS_PATH . 'atMessage.class.php';
require_once PHPANVIL_TOOLS_PATH . 'atDynamicObject.abstract.php';
require_once PHPANVIL_TOOLS_PATH . 'atRegional.class.php';


/**
 *
 * Primary processing controller for the phpAnvil framework.
 *
 * @author       Nick Slevkoff <nick@slevkoff.com>
 * @copyright    Copyright (c) 2009-2011 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup      phpAnvil
 */
class phpAnvil extends atDynamicObjectAbstract
{

    const VERSION = '1.8';
    const BUILD = '5';

    public $qsModule = 'anvil_module';
    public $qsAction = 'anvil_action';

    public $hideModuleQS = false;

    public $moduleOverride;
    public $actionOverride;

    public $session;

    public $actionMsg;
    public $errorMsg;
    public $pageMsg;

    public $controller = null;
    public $database = null;
//    public $listener;
    public $module = null;
    public $option = null;
    public $path = null;

    public $db = null;

    public $ui = null;

    public $site;
    public $application;
    public $userAuthenticated = false;


    private $_configContent = '';
    private $_eventListeners = array();
    private $_connectedEvents = array();


    public $regional = null;
    public $modelDictionary = null;


    public function __construct()
    {
        global $options;

        $this->enableLog();


        $this->addProperty('isNewSession', false);
        $this->addProperty('isNewUser', false);
        $this->addProperty('isBot', false);
        $this->addProperty('sourceTypeID', 0);
        $this->addProperty('sourceID', 0);


        $this->controller = new ControllerCollection();


        $this->database = new DatabaseCollection();


        $this->module = new ModuleCollection();

        $this->option = new OptionCollection();


        $this->path = new PathCollection();

        $this->regional = new atRegional();

        //		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Class Constructed.');
        $this->logVerbose('Class Constructed.');
    }


    function init()
    {
        $return = false;

        //        $this->webRootPath = str_replace('index.php', '', $_SERVER["SCRIPT_NAME"]);


        //---- Initialize Message Controls
        $this->actionMsg = new atMessage('ActionMsg', 'ActionMsg', 'msg');
        //        $this->actionMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iActionMsg.png';
        $this->actionMsg->singleBox = false;

        $this->errorMsg = new atMessage('ErrorMsg', 'ErrorMsg', 'msg');
        //        $this->errorMsg->enableTrace();
        //        $this->errorMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iErrorMsg.png';
        $this->errorMsg->singleBox = false;

        $this->pageMsg = new atMessage('PageMsg', 'PageMsg', 'msg');
        //        $this->pageMsg->icon = $phpAnvil->site->webPath . 'themes/default/images/iPageMsg.png';
        $this->pageMsg->singleBox = false;


        //---- Initialize Session Object
        $this->session = new atSession();


        $this->triggerEvent('phpAnvil.init');


        //---- Check if Application is Set
        if (isset($this->application)) {
            //---- Initialize the Application
            $return = $this->application->init();

            if ($return) {
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
            $this->logError('Application not set in phpAnvil.');
        }


        return $return;
    }


    function open()
    {
        #--- Set Server App Timezone
        if (version_compare(phpversion(), "5.1.0", ">")) {
            date_default_timezone_set($this->site->timeZone);
        }


        //---- Start Session
        $this->session->dataConnection = $this->db;
        //        $this->session->enableTrace();
        //        $this->session->innactiveTimeout = 60 * 60;
        $this->session->open();

        $this->regional->timezoneOffset = $this->session->timezoneOffset;

        //        FB::log('regional->timezoneOffset set.');
        //        FB::log('test');

        if (!empty($this->regional->timezoneOffset)) {
            $this->regional->dateTimeZone = new DateTimeZone('Etc/GMT' . $this->regional->timezoneOffset);
            //            FB::log('regional->dateTimeZone set.');
        }

        $this->triggerEvent('phpAnvil.open');


        //---- Check if Application is Set
        if (isset($this->application)) {
            //---- Open the Application
            $this->application->open();

        } else {
            //            FB::error('Application not set in phpAnvil.');
            $this->logError('Application not set in phpAnvil.');
        }

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
        $this->_eventListeners[] = $eventListener;
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

        $return = -1;
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

        $moduleRefName = strtolower($moduleRefName);
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
                //                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
                //                FB::info($msg);
                $this->logVerbose('Loading controller (' . $controllerName . ') for Module (' . $moduleRefName . ')...');


                //---- Build File Path to the Controller
                $filePath = 'modules/' . $moduleRefName . '/controllers/' . $controllerName . '.controller.php';

                if (file_exists(APP_PATH . $filePath)) {
                    $filePath = APP_PATH . $filePath;
                } else {
                    if (file_exists(PHPANVIL_FRAMEWORK_PATH . $filePath)) {
                        $filePath = PHPANVIL_FRAMEWORK_PATH . $filePath;
                    } else
                    {
                        if (isset($this->application->catchAllAction) && $this->application->catchAllAction != $controllerName) {
                            $this->loadController($this->application->catchAllModule, $this->application->catchAllAction);
                        } else {
                            $this->logError('Controller (' . $controllerName . ') for Module (' . $moduleRefName . ') not found.');
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
                        //                        $this->logDebug($fullControllerName, '$fullControllerName');
                        //                        $this->logDebug($controllerClassName, '$controllerClassName');

                        $this->controller[$fullControllerName] = new $controllerClassName();
                    }

                    $this->controller[$fullControllerName]->module = $this->module[$moduleRefName];
                    $this->controller[$fullControllerName]->refName = $controllerName;

                    $return = $this->controller[$fullControllerName]->init();
                    if ($return) {
                        //                        if (!empty($_GET)) {
                        $return = $this->controller[$fullControllerName]->processGET();
                        //                        }
                        if ($return && !empty($_POST)) {
                            $return = $this->controller[$fullControllerName]->processPOST();
                        }
                        if ($return && empty($this->controller[$fullControllerName]->redirectURL)) {
                            $return = $this->controller[$fullControllerName]->open();
                            if (!$return) {
                                //                            FB::warn('Controller (' . $fullControllerName . ') failed to open.');
                                $this->logWarning('Controller (' . $fullControllerName . ') failed to open.');
                                //                            header('Location: ' . $phpAnvil->controller[$fullControllerName]->redirectURL);
                            }
                        }
                        $return = $this->controller[$fullControllerName]->close();

                    }

                    if (!empty($this->controller[$fullControllerName]->redirectURL)) {
                        $this->logVerbose('Redirecting...');
                        header('Location: ' . $this->controller[$fullControllerName]->redirectURL);

                    } else {
                        //                        FB::warn('Controller (' . $fullControllerName . ') failed to init.');
                        //                        $this->logWarning('Controller (' . $fullControllerName . ') failed to init.');
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
                $this->logError('Unable to load the module, both the passed reference name and default module name are undefined.');
            }
        }

        $return = !empty($moduleRefName);
        //        $return = true;

        $moduleRefName = strtolower($moduleRefName);


        if ($return && !$this->module->contains($moduleRefName)) {
            //        if (!$this->module->contains($moduleRefName)) {

            //            $msg = 'Loading Module (' . $moduleRefName . ')...';
            //			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_DEBUG);
            //            FB::info($msg);
            $this->logVerbose('Loading Module (' . $moduleRefName . ')...');

            $filePath = 'modules/' . $moduleRefName . '/' . $moduleRefName . '.module.php';


            //            FB::log($filePath, '$filePath');

            if (file_exists(APP_PATH . $filePath)) {
                $filePath = APP_PATH . $filePath;
            } else {
                if (file_exists(PHPANVIL_FRAMEWORK_PATH . $filePath)) {
                    $filePath = PHPANVIL_FRAMEWORK_PATH . $filePath;
                } else
                {
                    //                FB::error('Module (' . $moduleRefName . ') not found.  Please check module reference name and that the module has been installed on the server.');
                    $this->logError('Module (' . $moduleRefName . ') not found.  Please check module reference name and that the module has been installed on the server.');
                    $return = false;
                }
            }

            //            $this->logDebug($filePath, '$filePath');


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
                    $this->logWarning('Module (' . $moduleRefName . ') failed to init.');
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
        $this->logVerbose('Loading all custom modules...');

        $count = count($moduleDirectories);
        for ($i = 0; $i < $count; $i++)
        {
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
        $max = count($moduleKeys);
        for ($i = 0; $i < $max; $i++)
        {
            $this->module[$moduleKeys[$i]]->open();
        }
    }


    public function prepareNewAction($source, $module, $type, $data)
    {

        $action = new Action();
        $action->source = $source;
        $action->module = $module;
        $action->type = $type;
        $action->data = $data;

        return $action;
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

            for ($i = 0; $i < $max; $i++)
            {
                $return = $this->module[$i]->processAction($action);
            }

        }

        return $return;
    }


    public function processAjaxAction()
    {

        $objAjax = new atAjax();
        $ajaxPacket = $_POST['request_packet'];

        $ajaxPacket = str_replace('\"', '"', $ajaxPacket);
        $requestPacket = json_decode($ajaxPacket);

        foreach ($requestPacket as $ajaxRequest) {

            $moduleID = $ajaxRequest->moduleID;
            $moduleCode = $this->getModuleCode($moduleID);
            $moduleActionID = $ajaxRequest->moduleActionID;

            //			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Processing Ajax Action (' . $moduleActionID . ') for Module (' . $moduleCode . ')', self::TRACE_TYPE_DEBUG);
            $this->logVerbose('Processing Ajax Action (' . $moduleActionID . ') for Module (' . $moduleCode . ')');

            $data['sourceID'] = $ajaxRequest->sourceID;
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
        $widget = strtolower($widget);
        //		$moduleCode = $this->getModuleCode($module);
        //		$moduleID = $moduleIDs[strtolower($moduleCode)];

        if ($this->loadModule($moduleRefName)) {
            //		if (is_array($moduleIDs) && array_key_exists(strtolower($moduleCode), $moduleIDs)) {
            //            $msg = 'Loading Widget (' . $moduleRefName . "." . $widget . ')...';
            //			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
            //            FB::info($msg);
            $this->logVerbose('Loading Widget (' . $moduleRefName . "." . $widget . ')...');


            $filePath = 'modules/' . $moduleRefName . '/widgets/' . $widget . '.widget.php';

            //            FB::log($filePath, '$filePath');

            if ($this->module[$moduleRefName]->type === BaseModule::TYPE_CUSTOM) {
                $filePath = APP_PATH . $filePath;
            } else
            {
                $filePath = PHPANVIL_FRAMEWORK_PATH . $filePath;
            }

            //            FB::log($filePath, '$filePath');

            include_once $filePath;

            $return = true;

            //		} else {

            //			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, "Module '" . $moduleCode . "' not installed.", self::TRACE_TYPE_DEBUG);
        }

        return $return;

    }


    public function loadDictionary($dictionary)
    {
        //		global $modules, $moduleIDs, $moduleTypes;
        //		global $options, $phrases;
        global $phrases;

        $dictionary = strtolower($dictionary);

        //		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, "Loading Dictionary '" . $dictionary . "'...", self::TRACE_TYPE_DEBUG);

        $filePath = APP_PATH . 'lang/' . 'en/' . $dictionary . '.dictionary.php';

        return require_once($filePath);
    }


    public function execute()
    {
        //        FB::group('Initializing...');
        $this->logGroup('Initializing...');

        if ($this->init()) {
            $this->logGroupEnd();
            //            FB::groupEnd();
            //            FB::group('Opening...');
            $this->logGroup('Opening...');

            $this->open();

            $this->logGroupEnd();
            //            FB::groupEnd();
            //            FB::group('Closing...');
            $this->logGroup('Closing...');

            $this->close();
        }
        $this->logGroupEnd();
        //        FB::groupEnd();
        $this->logInfo('END OF LINE.');
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
            $this->logError('Application not set in phpAnvil.');
        }


        $this->triggerEvent('phpAnvil.close');


        $this->session->close();

        //        $this->logDebug('session closed');

        sendDebugTrace();
        //		$this->db->close();

    }


    public function triggerEvent($event, $parameters = '')
    {
        $return = false;

        if (array_key_exists($event, $this->_connectedEvents)) {

            //            $msg = 'Triggering event (' . $event . ')...';
            //            $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_INFO);
            //            FB::info($msg);
            $this->logVerbose('Triggering event (' . $event . ')...');

            //---- Loop through All Connected Event Listeners for Matches
            $max = count($this->_eventListeners);
            for ($i = 0; $i < $max; $i++)
            {
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
                        $this->logWarning('Invalid Event Listener (' . $this->_eventListeners[$i]->callback . ')!');
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
        $msg = "Uninstalling Module '" . $moduleCode . "'...";

        $newModule = new ModuleModel($this->db);

        if ($newModule->loadCode($moduleCode)) {
            //            $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_DEBUG);
            //            $this->logVerbose($msg);

            //---- Determine Module Type
            $filePath = 'modules/' . $moduleCode . '/';
            if ($moduleTypes[$newModule->id] == MODULE_TYPE_CUSTOM) {
                $filePath = APP_PATH . $filePath;
            } else {
                $filePath = PHPANVIL_FRAMEWORK_PATH . $filePath;
            }

            if (file_exists($filePath)) {
                $filePath .= $moduleCode . '.install.php';

                //                FB::log($filePath);

                include_once($filePath);

                $installers[$moduleCode]->uninstall($newModule);
            } else {
                $msg = 'Module (' . $moduleCode . ') does not exist.';
                //                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $msg, self::TRACE_TYPE_ERROR);
                $return = false;
            }
        } else {
            $msg = 'Module (' . $moduleCode . ') is not installed.';
            $return = false;
        }
        //        FB::log($msg);
        $this->logVerbose($msg);


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


            switch (true)
            {
                // If difference is less than 60 seconds,
                // seconds is a good interval of choice
                case(strtotime('-1 min', $toDTS) < $fromDTS):
                    $datediff = $difference;
                    $return = ($datediff == 1) ? $datediff . ' second ago'
                            : $datediff . ' seconds ago';
                    break;
                // If difference is between 60 seconds and
                // 60 minutes, minutes is a good interval
                case(strtotime('-1 hour', $toDTS) < $fromDTS):
                    $datediff = floor($difference / 60);
                    $return = ($datediff == 1) ? $datediff . ' minute ago'
                            : $datediff . ' minutes ago';
                    break;
                // If difference is between 1 hour and 24 hours
                // hours is a good interval
                case(strtotime('-1 day', $toDTS) < $fromDTS):
                    $datediff = floor($difference / 60 / 60);
                    $return = ($datediff == 1) ? $datediff . ' hour ago'
                            : $datediff . ' hours ago';
                    break;
                // If difference is between 1 day and 7 days
                // days is a good interval
                case(strtotime('-1 week', $toDTS) < $fromDTS):
                    $day_difference = 1;
                    while (strtotime('-' . $day_difference . ' day', $toDTS) >= $fromDTS)
                    {
                        $day_difference++;
                    }

                    $datediff = $day_difference;
                    $return = ($datediff == 1) ? 'yesterday'
                            : $datediff . ' days ago';
                    break;
                // If difference is between 1 week and 30 days
                // weeks is a good interval
                case(strtotime('-1 month', $toDTS) < $fromDTS):
                    $week_difference = 1;
                    while (strtotime('-' . $week_difference . ' week', $toDTS) >= $fromDTS)
                    {
                        $week_difference++;
                    }

                    $datediff = $week_difference;
                    $return = ($datediff == 1) ? 'last week'
                            : $datediff . ' weeks ago';
                    break;
                // If difference is between 30 days and 365 days
                // months is a good interval, again, the same thing
                // applies, if the 29th February happens to exist
                // between your 2 dates, the function will return
                // the 'incorrect' value for a day
                case(strtotime('-1 year', $toDTS) < $fromDTS):
                    $months_difference = 1;
                    while (strtotime('-' . $months_difference . ' month', $toDTS) >= $fromDTS)
                    {
                        $months_difference++;
                    }

                    $datediff = $months_difference;
                    $return = ($datediff == 1) ? $datediff . ' month ago'
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
                    while (strtotime('-' . $year_difference . ' year', $toDTS) >= $fromDTS)
                    {
                        $year_difference++;
                    }

                    $datediff = $year_difference;
                    $return = ($datediff == 1) ? $datediff . ' year ago'
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
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }


    public function ago($datetime)
    {
        $interval = date_create('now')->diff($datetime);
        $suffix = ($interval->invert ? ' ago' : '');
        if ($v = $interval->y >= 1) {
            return $this->pluralize($interval->y, 'year') . $suffix;
        }
        if ($v = $interval->m >= 1) {
            return $this->pluralize($interval->m, 'month') . $suffix;
        }
        if ($v = $interval->d >= 1) {
            return $this->pluralize($interval->d, 'day') . $suffix;
        }
        if ($v = $interval->h >= 1) {
            return $this->pluralize($interval->h, 'hour') . $suffix;
        }
        if ($v = $interval->i >= 1) {
            return $this->pluralize($interval->i, 'minute') . $suffix;
        }
        return $this->pluralize($interval->s, 'second') . $suffix;
    }


    public function ago2($i)
    {
        $m = time() - $i;
        $o = 'just now';
        $t = array('year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1);
        foreach ($t as $u => $s) {
            if ($s <= $m) {
                $v = floor($m / $s);
                $o = "$v $u" . ($v == 1 ? '' : 's') . ' ago';
                break;
            }
        }
        return $o;
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


    public function getDTS($dts = '', $timeZone = '', $format = '')
    {
        if (empty($dts)) {
            //            $dts = date('c');
            //            $dts = new DateTime(null, $this->regional->dateTimeZone);
            $dts = null;
        }

        if (!empty($timeZone)) {
            $value = new DateTime($dts, new DateTimeZone($timeZone));
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


    public function generateToken($length = 8)
    {
        $charset = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $maxLen = strlen($charset) - 1;

        $token = '';
        for ($i = 0; $i < $length; $i++)
        {
            $token .= $charset[rand(0, $maxLen)];
        }

        return $token;
    }
}

?>
