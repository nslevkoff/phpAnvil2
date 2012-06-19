<?php
/**
 * @file
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *     This source file is subject to the new BSD license that is
 *     bundled with this package in the file LICENSE.txt. It is also
 *     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup         phpAnvilTools anvilFuse
 */


require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';


/**
 * anvilFuseTrap Class
 *
 * @version        1.0
 * @date            8/26/2010
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilFuse
 */
class anvilFuseTrap extends anvilDynamicObjectAbstract
{
    const VERSION = '1.0';


    #----------------------------------------------
    #---- Event Type Constants

    const EVENT_TYPE_OTHER = 0;
    const EVENT_TYPE_TEST = 7;
    const EVENT_TYPE_INFO = 5;
    const EVENT_TYPE_DEBUG = 6;
    const EVENT_TYPE_WARNING = 3;
    const EVENT_TYPE_ERROR = 2;
    const EVENT_TYPE_CRITICAL = 1;

    #----------------------------------------------
    #---- Error Handler Properties

    private $_oldErrorHander;
    private $_errorHandlerEnabled = false;
    private $_errorHandlerEventType = array();
    private $_errorHandlerTypes = array();
    private $_errorHandlerCallback = array();
    private $_errorHandlerDieAfter = array();

    #----------------------------------------------
    #---- Exception Handler Properties

    private $_oldExceptionHandler;
    private $_exceptionHandlerEnabled = false;
    private $_exceptionHandlerCallback;
    private $_exceptionHandlerDieAfter = false;

    #----------------------------------------------
    #---- anvilFuseTrap Server Properties

    private $_serverEnabled = false;
    public $applicationID = 0;
    public $applicationVersion = '';
    public $serverURL = '';

    #----------------------------------------------
    #---- anvilFuseTrap Database Properties
    private $_databaseEnabled = false;
    public $dataConnection = null;
    public $tableName = 'events';
    public $tableVersion = 1;

    public $userID = 0;

    #----------------------------------------------
    #---- FireBug Properties
    private $_fireBugEnabled = false;
    private $_firePHP = null;

    #----------------------------------------------
    #---- anvilFuseTrap log Properties
    public $logPath = '';

    public $echoErrors = true;
    public $echoExceptions = true;
    public $echoTrace = false;

    private $_isConsole = false;
    private $_remoteIP = '';

    #----------------------------------------------
    #---- anvilFuseTrap Methods


    public function __construct()
    {
//        $this->enableLog();
        $this->_isConsole = PHP_SAPI == 'cli';

        if (!$this->_isConsole) {
            $this->_remoteIP = $_SERVER['REMOTE_ADDR'];
        }
    }


    public function disableServer()
    {
        $this->_serverEnabled = false;
    }


    public function enableServer()
    {
        if (!empty($this->serverURL)) {
            // Turn off all error reporting
            error_reporting(0);
            try {
                if (fopen($this->serverURL, "r")) {
                    $this->_serverEnabled = true;
                }
            } catch (Exception $exception) {
            }
            // Report all errors except E_NOTICE
            // This is the default value set in php.ini
            error_reporting(E_ALL ^ E_NOTICE);
        }
    }


    public function isServerEnabled()
    {
        return $this->_serverEnabled;
    }


    public function disableDatabase()
    {
        $this->_databaseEnabled = false;
    }


    public function enableDatabase()
    {
        if (!empty($this->tableName) && $this->dataConnection != null) {
            $this->_databaseEnabled = true;
        }
    }


    public function isDatabaseEnabled()
    {
        return $this->_databaseEnabled;
    }


    public function disableFireBug()
    {
        $this->_fireBugEnabled = false;
    }


    public function enableFireBug($firePHP)
    {
        $this->_firePHP = $firePHP;
        $this->_fireBugEnabled = true;
    }


    public function isFireBugEnabled()
    {
        return $this->_fireBugEnabled;
    }


    public function executeErrorHandler($type, $message, $file, $line)
    {
        global $firePHP;

        //if (TRACE) anvilFuseTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, "parameters = ($errNo, $errFile, $errLine)");
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        //        FB::info('Error Detected! Processing...');

        try {

            #---- Format PHP's Debug Backtrace for Attaching to the Message
            $backtraceString = "\nPHP Trace:\n";
            $backtraceArray = debug_backtrace();
            foreach ($backtraceArray as $rowNum => $backtrace) {
                //				$backtraceString .= '#' . $rowNum . ' ' . @var_export($backtrace, true);

                //				$firePHP->_log($backtrace);

                $backtraceString .= '#' . $rowNum . ' ';
                if (array_key_exists('file', $backtrace)) {
                    $backtraceString .= $backtrace['file'];
                }
                if (array_key_exists('line', $backtrace)) {
                    $backtraceString .= ' (line ' . $backtrace['line'] . ')';
                }
                $backtraceString .= ': ';
                if (array_key_exists('class', $backtrace)) {
                    $backtraceString .= 'class=' . $backtrace['class'];
                }
                if (array_key_exists('type', $backtrace)) {
                    $backtraceString .= ', type=' . $backtrace['type'];
                }
                if (array_key_exists('function', $backtrace)) {
                    $backtraceString .= ', function=' . $backtrace['function'];
                }

                if (array_key_exists('args', $backtrace)) {
                    $args = $backtrace['args'];
                    if (!empty($args)) {
                        $backtraceString .= ' (';
                        foreach ($backtrace['args'] as $argNum => $argValue) {
                            if ($argNum > 0) {
                                $backtraceString .= ', ';
                            }
                            if (is_object($argValue)) {
                                $backtraceString .= get_class($argValue);
                            } else {
                                $backtraceString .= '"' . $argValue . '"';
                            }
                        }
                        $backtraceString .= ')';
                    }
                }
                $backtraceString .= "\n";

            }
            $message .= "\n" . $backtraceString;

            #---- Add POST Array to the Message
            if (count($_POST) > 0) {
                $postString = "\nPOST:\n";
                foreach ($_POST as $key => $value) {
                    $postString .= $key . '=' . $value . "\n";
                }
                $message .= "\n" . $postString;
            }

            if (count($_GET) > 0) {
                $getString = "\nGET:\n";
                foreach ($_GET as $key => $value) {
                    $getString .= $key . '=' . $value . "\n";
                }
                $message .= "\n\n" . $getString;
            }


            foreach ($this->_errorHandlerTypes as $key => $types) {
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$key=' . $key);
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$type=' . $type);
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$types=' . var_export($types, true));

                if (in_array($type, $types)) {

                    $number = $type;

                    //---- Prepare Subject
                    $subject = strip_tags($message);
                    $brief = $message;
                    if (strlen($brief) > 1024) {
                        $brief = substr($brief, 0, 1024) . '...';
                    }
                    if (strlen($subject) > 50) {
                        $subject = substr($subject, 0, 50) . '...';
                    }
                    $subject = $subject . ' on line ' . $line;

                    //---- FireBug Console Output (via FirePHP)
                    $this->_logGroup('anvilFuseTrap Error: ' . $subject,
                        array('Collapsed' => true,
                            'Color' => '#F00000'));

                    $this->_logError($brief);

                    $messages = explode("\n", $message);
                    $max = count($messages);
                    if ($max > 4) {
                        $max = 4;
                    }
                    for ($i = 0; $i < $max; $i++)
                    {
//                        $this->_logInfo($messages[$i]);
                        $this->_logWarning($messages[$i]);
                    }

                    $this->_logError($file, 'File');
                    $this->_logError($line, 'Line');

                    $messages = explode("\n", $message);
                    $max = count($messages);
                    for ($i = 0; $i < $max; $i++)
                    {
                        if ($this->echoErrors) {
                            echo ($messages[$i] . "<br />\n");
                        }
                        $this->_logInfo($messages[$i]);
//                        $this->_logWarning($messages[$i]);
                    }

                    $this->_logGroupEnd();
                    //----------------------------------------


                    if ($this->isServerEnabled() || $this->isDatabaseEnabled()) {

                        $this->sendToServer($this->_errorHandlerEventType[$key], $subject, $number, $message, $file, $line);
                    }

                    if (!empty($this->_errorHandlerCallback[$key])) {
                        //if (TRACE) anvilFuseTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Calling function ' . $this->_errorHandlerCallback[$key] . '...');

                        call_user_func($this->_errorHandlerCallback[$key], $number, $message, $file, $line);
                    }

                    if ($this->_errorHandlerDieAfter[$key]) {
                        die();
                    }
                }

            }

        } catch (Exception $error) {
            echo('anvilFuseTrap ERROR: [' . $error->getCode() . '] ' . $error->getMessage() . ' on line ' . $error->getLine() . '<br>');
        }

        return true;
    }


    /**
     * anvilFuseTrap's exception handler.
     */
    public function executeExceptionHandler($exception)
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        //        FB::info('Exception Detected! Processing...');

        try {
            $number = $exception->getCode();
            $message = '[' . $exception->getCode() . '] ' . $exception->getMessage();
            $message .= "\n" . $exception->getTraceAsString();
            $file = $exception->getFile();
            $line = $exception->getLine();

            #---- Add POST Array to the Message
            if (count($_POST) > 0) {
                $postString = "\nPOST:\n";
                foreach ($_POST as $key => $value) {
                    $postString .= $key . '=' . $value . "\n";
                }
                $message .= "\n\n" . $postString;
            }

            if (count($_GET) > 0) {
                $getString = "\nGET:\n";
                foreach ($_GET as $key => $value) {
                    $getString .= $key . '=' . $value . "\n";
                }
                $message .= "\n\n" . $getString;
            }


            //---- Prepare Subject
            $subject = strip_tags($message);
            if (strlen($subject) > 50) {
                $subject = substr($subject, 0, 50) . '...';
            }
            $subject = $subject . ' on line ' . $line;

            //---- FireBug Console Output (via FirePHP)
            //            FB::group('anvilFuseTrap Exception: ' . $subject,
            //                array('Collapsed' => true,
            //                      'Color' => '#FF00FF'));

            $this->_logGroup('anvilFuseTrap Exception: ' . $subject,
                array('Collapsed' => true,
                    'Color' => '#FF00FF'));

            $this->_logError($file, 'File');
            $this->_logError($line, 'Line');

            $messages = explode("\n", $message);
            $max = count($messages);
            for ($i = 0; $i < $max; $i++)
            {
                if ($this->echoExceptions) {
                    echo ($messages[$i] . "<br />\n");
                }
                $this->_logInfo($messages[$i]);
            }

            $this->_logGroupEnd();
            //----------------------------------------


            if ($this->isServerEnabled() || $this->isDatabaseEnabled()) {

                $this->sendToServer(self::TRACE_TYPE_CRITICAL, $subject, $number, $message, $file, $line);
            }


            if (!empty($this->_exceptionHandlerCallback)) {
                call_user_func($this->_exceptionHandlerCallback, $exception);
            }

            if ($this->_exceptionHandlerDieAfter) {
                die();
            }

        } catch (Exception $error2) {
            echo('anvilFuseTrap ERROR: [' . $error2->getCode() . '] ' . $error2->getMessage() . ' on line ' . $error2->getLine() . '<br>');
        }

        return true;
    }


    /**
     * Add an error handler callback.
     */
    public function onError($eventType = self::EVENT_TYPE_ERROR, $types = array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), $callback = '', $dieAfter = false)
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        //if (TRACE) anvilFuseTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, "parameters = ($types, $callback, $dieAfter)");
        $this->_errorHandlerEventType[] = $eventType;
        $this->_errorHandlerTypes[] = $types;
        $this->_errorHandlerCallback[] = $callback;
        $this->_errorHandlerDieAfter[] = $dieAfter;

        //return $this->enableErrorHandler();
    }


    /**
     * Add an exception handler callback.
     */
    public function onException($types, $callback, $dieAfter = false)
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        //if (TRACE) anvilFuseTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, "parameters = ($types, $callback, $dieAfter)");
        $this->_exceptionHandlerTypes[] = $types;
        $this->_exceptionHandlerCallback[] = $callback;
        $this->_exceptionHandlerDieAfter[] = $dieAfter;

        //		return $this->enableErrorHandler();
    }


    public function sendToServer($type, $subject, $number, $message, $file, $line)
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');

        if ($this->_isTraceDefined()) {
            $trace = anvilFuseTrace::render();

            //			$this->_firePHP->_log('Trace Defined');

            if ($this->echoTrace && $type > self::TRACE_TYPE_DEBUG) {
                echo(nl2br($message));
                echo(anvilFuseTrace::renderHTML());
                echo('<hr />');
            }
        }

        if ($this->isFireBugEnabled()) {
            //			$this->_firePHP->_log(array($type, $subject, $number, $message, $file, $line, $trace));
        }

        if ($this->isServerEnabled()) {
            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Opening Connection to anvilFuseTrap Server...');
            $this->_logVerbose('Opening Connection to anvilFuseTrap Server...');

            try {
                $anvilFuseWS = new SoapClient($this->serverURL);

                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Adding new event...');
                //                $this->_logVerbose('Adding new event...');


                $return = $anvilFuseWS->newEvent(
                    $type,
                    $this->applicationID,
                    $this->applicationVersion,
                    $this->_remoteIP,
                    $subject,
                    $number,
                    $message,
                    $file,
                    $line,
                    $trace);

                return $return;

            } catch (SoapFault $fault) {
                $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Server communications failure! (' . $fault->faultcode . ' - ' . $fault->faultstring . ')');
            } catch (Exception $exception) {
                $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Server communications failure! (' . $exception->getCode() . ' - ' . $exception->getMessage() . ')');
            }
            #----------------------------------------
        } elseif ($this->isDatabaseEnabled()) {
            //			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Opening Connection to Database...');
            $this->_logVerbose('Opening Connection to Database...');

            try {
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'starting...');
                //                $this->_logVerbose('starting...');

                if ($this->tableVersion === 1) {
                    $objEvent = new anvilFuseEvent($this->dataConnection, $this->tableName);

                    $objEvent->eventTypeID = $type;
                    $objEvent->applicationID = $this->applicationID;

                } else {
                    $objEvent = new anvilFuseEvent2($this->dataConnection, $this->tableName);

                    $objEvent->fuseEventTypeID = $type;
                    $objEvent->fuseApplicationID = $this->applicationID;

                }
                $objEvent->version = $this->applicationVersion;
                $objEvent->userIP = $this->_remoteIP;
                $objEvent->userID = $this->userID;
                $objEvent->name = substr($subject, 0, 255);
                $objEvent->number = $number;
                $objEvent->details = $message;
                $objEvent->file = $file;
                $objEvent->line = $line;
                $objEvent->trace = $trace;

                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Save SQL = ' . $objEvent->buildSaveSQL());

                $objEvent->save();

                $return = array('result' => '1', 'junk' => 'junk');

                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$return=' . var_export($return, true));

                return $return;
            } catch (Exception $exception) {
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Server communications failure! (' . $exception->getCode() . ' - ' . $exception->getMessage . ")\n[" . $exception->getTraceAsString() . ']');
                $this->_logError('Server communications failure! (' . $exception->getCode() . ' - ' . $exception->getMessage() . ')');
            }

        } elseif (!empty($this->_logPath)) {
            $types = array(0 => 'Other', 10 => 'Test', 20 => 'Info', 30 => 'Debug', 40 => 'Warning', 50 => 'Error', 60 => 'Critical');

            try {
                $log = fopen($this->_logPath, 'ab');

                $logAppend = "-------------------------------------------------------\r\n";
                $logAppend .= 'DTS: ' . date('Y-m-d H:i:s') . "\r\n";
                $logAppend .= 'Type: ' . $types[$type] . "\r\n";
                $logAppend .= 'Application ID: ' . $this->applicationID . "\r\n";
                $logAppend .= 'Application version: ' . $this->applicationVersion . "\r\n";
                $logAppend .= 'User IP: ' . $this->_remoteIP . "\r\n";
                $logAppend .= 'Number: ' . $number . "\r\n";
                $logAppend .= 'Name: ' . $subject . "\r\n";
                $logAppend .= 'Details: ' . $message . "\r\n";
                $logAppend .= 'File: ' . $file . "\r\n";
                $logAppend .= 'Line: ' . $line . "\r\n";
                $logAppend .= "\r\n";
                $logAppend .= $trace;
                $logAppend .= "-------------------------------------------------------\r\n\r\n\r\n\r\n";

                fwrite($log, $logAppend);
                fclose($log);

                $return = array('result' => '1',
                    'junk' => 'junk');

                return $return;
            } catch (Exception $exception) {
                //				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Server communications failure! (' . $exception->code . ' - ' . $exception->message . ')');
                $this->_logError('Server communications failure! (' . $exception->getCode() . ' - ' . $exception->getMessage() . ')');
            }

        } else {
            return false;
        }
    }


    /**
     * Start all anvilFuseTrap processing.
     */
    public function start()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        $this->startErrorHandler();
        $this->startExceptionHandler();
    }


    /**
     * Start anvilFuseTrap's error handler.
     */
    public function startErrorHandler()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        if (!$this->_errorHandlerEnabled) {
            $this->_errorHandlerEnabled = true;
            $this->_oldErrorHander = set_error_handler(array($this, 'executeErrorHandler'));
        }

        return true;
    }


    /**
     * Start anvilFuseTrap's exception handler.
     */
    public function startExceptionHandler()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        if (!$this->_exceptionHandlerEnabled) {
            $this->_exceptionHandlerEnabled = true;
            $this->_oldExceptionHandler = set_exception_handler(array($this, 'executeExceptionHandler'));
        }

        return true;
    }


    /**
     * Stop all anvilFuseTrap processing.
     */
    public function stop()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        $this->stopErrorHandler();
        $this->stopExceptionHandler();
    }


    /**
     * Stops the anvilFuseTrap error handler and restores the prior error handler.
     */
    public function stopErrorHandler()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        if ($this->_errorHandlerEnabled) {
            $this->_errorHandlerEnabled = false;
            set_error_handler($this->_oldErrorHander);
        }

        return true;
    }


    /**
     * Stops the anvilFuseTrap error handler and restores the prior error handler.
     */
    public function stopExceptionHandler()
    {
        //		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');
        //        $this->_logVerbose('Executing...');
        if ($this->_exceptionHandlerEnabled) {
            $this->_exceptionHandlerEnabled = false;
            set_exception_handler($this->_oldExceptionHandler);
        }

        return true;
    }

}

?>