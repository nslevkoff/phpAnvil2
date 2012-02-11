<?php
/**
 * @file
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2010-2011 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *     This source file is subject to the new BSD license that is
 *     bundled with this package in the file LICENSE.txt. It is also
 *     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 */

/**
 * Base parent object for all phpAnvil classes.
 *
 * @version       1.5
 * @date          8/18/2011
 * @author        Nick Slevkoff <nick@slevkoff.com>
 * @copyright     Copyright (c) 2010-2011 Nick Slevkoff (http://www.slevkoff.com)
 */
abstract class atObjectAbstract
{
    /**
     * Version number for this class release.
     */
    const VERSION = '1.0';

    
    //==== NEW Logging Constants ===============================================

    /**
     * Constant value disables all logging.
     */
    const LOG_LEVEL_DISABLED = 0;

    /**
     * Constant value sets logging to critical errors only.
     */
    const LOG_LEVEL_CRITICAL = 1;

    /**
     * Constant value sets logging to errors and critical errors.
     */
    const LOG_LEVEL_ERROR = 2;

    /**
     * Constant value sets logging to warnings and above.
     */
    const LOG_LEVEL_WARNING = 3;

    /**
     * Constant value sets logging to brief info and above.
     */
    const LOG_LEVEL_BRIEF_INFO = 4;

    /**
     * Constant value sets logging to verbose info and above.
     */
    const LOG_LEVEL_VERBOSE_INFO = 5;

    /**
     * Constant value sets logging to debug entries and above.
     */
    const LOG_LEVEL_DEBUG = 6;

    /**
     * Constant value sets logging to all entries.
     */
    const LOG_LEVEL_ALL = 99;


    /**
     * Integer indicating the current log level.
     */
    public $logLevel = self::LOG_LEVEL_WARNING;


    //==== OLD Trace Constants =================================================
    /**
     * Disables all tracing.
     */
    const TRACE_LEVEL_DISABLED = 0;
    /**
     * Allows the tracing of errors only.
     */
    const TRACE_LEVEL_ERRORS = 2;
    /**
     * Allows the tracing of errors and warnings only.
     */
    const TRACE_LEVEL_WARNINGS = 3;
    /**
     * Allows the tracing of errors, warnings, and debug lines only.
     */
    const TRACE_LEVEL_DEBUG = 6;
    /**
     * Allows all tracing levels rated at 75 and below.
     */
    const TRACE_LEVEL_VERBOSE = 5;
    /**
     * Allows all tracing levels.
     */
    const TRACE_LEVEL_ALL = 99;


    /**
     * Some custom tracing type.
     */
    const TRACE_TYPE_OTHER = 0;
    /**
     * Trace line useful during testing.
     */
    const TRACE_TYPE_TEST = 7;
    /**
     * Trace line useful as generic informational.
     */
    const TRACE_TYPE_INFO = 5;
    /**
     * Trace line useful for debugging.
     */
    const TRACE_TYPE_DEBUG = 6;
    /**
     * Trace line contains warning level information.
     */
    const TRACE_TYPE_WARNING = 3;
    /**
     * Trace line contains error level information.
     */
    const TRACE_TYPE_ERROR = 2;
    /**
     * Trace line contains critical error level information.
     */
    const TRACE_TYPE_CRITICAL = 1;


    /**
     * Integer indicating the current tracing level [#TRACE_LEVEL_DISABLED].
     */
    public $traceLevel = self::TRACE_LEVEL_DISABLED;


    public function __construct()
    {
    }


    //==== OLD Trace Functions for Backwards Compatibility =====================
    /**
     * Adds an info line to the trace.
     *
     * @param $file
     *     A string containing the name of the file (__FILE__) the trace info
     *     is from.
     * @param $method
     *     A string containing the name of the method (__METHOD__) the trace
     *     info is from.
     * @param $line
     *     An integer indicating the line number (__LINE__) the trace info
     *     is from.
     * @param $info
     *     A string containing the info for the trace.
     * @param $type
     *     (optional) An integer indicating the type of trace.  See the
     *     TRACE_TYPE_* constants for options. [#TRACE_TYPE_DEBUG]
     * @param $level
     *     (optional) An integer indicating the trace level.  See the
     *     TRACE_LEVEL_* constants for options. [#TRACE_LEVEL_DEBUG]
     */
    protected function addTraceInfo($file, $method, $line, $info,
        $type = self::TRACE_TYPE_DEBUG,
        $level = self::TRACE_LEVEL_DEBUG)
    {
        if ($level <= $this->traceLevel && $this->isTraceEnabled()
            && $this->isTraceDefined()
        ) {
            atFuseTrace::add($file, $method, $line, $info, $type);
        }
    }


    /**
     * Disables tracing by setting the trace level to #TRACE_LEVEL_DISABLED.
     */
    public function disableTrace()
    {
        $this->traceLevel = self::TRACE_LEVEL_DISABLED;
    }


    /**
     * Enables tracing.
     *
     * @param $traceLevel
     *   (optional) An integer indicating the trace level to start tracing at.
     */
    public function enableTrace($traceLevel = self::TRACE_LEVEL_ALL)
    {
        $this->traceLevel = $traceLevel;
    }


    /**
     * Returns whether the atTrace class is loaded and available.
     *
     * @return
     *   TRUE if the atTrace class is loaded and available for use, FALSE if not.
     */
    protected function isTraceDefined()
    {
        return class_exists('atFuseTrace', false);
    }


    /**
     * Returns whether tracing has been enabled.
     *
     * @return
     *   TRUE if the trace level is NOT set to #TRACE_LEVEL_DISABLED.
     */
    public function isTraceEnabled()
    {
        return $this->traceLevel > self::TRACE_LEVEL_DISABLED;
    }


    //==== NEW Logging Functions ===============================================
    /**
     * Adds a log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     * @param integer $logLevel
     * (optional) The importance level of the log entry.

     * @return NULL
     */
    protected function log($detail, $title = '', $logLevel = self::LOG_LEVEL_DEBUG)
    {
        global $phpAnvil;

        if (isset($phpAnvil) && $logLevel <= $this->logLevel && $this->isLogEnabled()) {
            $backTrace = debug_backtrace();

            //---- Add to atFuseTrace ------------------------------------------
            if ($this->isTraceDefined()) {
                if (!empty($title)) {
                    $traceInfo = $title . ' = ' . $detail;
                } else {
                    $traceInfo = $detail;
                }
                atFuseTrace::add($backTrace[1]['file'], $backTrace[2]['class'] . '::' . $backTrace[2]['function'], $backTrace[1]['line'], $traceInfo, $logLevel);
            }

//            $extendedTitle = '[' . $backTrace[2]['class'] . '->' . $backTrace[2]['function'] . ': Line ' . $backTrace[1]['line'] . '] ' . $title;
            $extendedTitle = $backTrace[2]['class'] . '->' . $backTrace[2]['function'] . ' (' . $backTrace[1]['line'] . ') ' . $title;

            //---- Output to FirePHP/FireBug -----------------------------------
            switch ($logLevel)
            {
                case self::LOG_LEVEL_DEBUG:
                    fb::log($detail, $extendedTitle);
                    break;

                case self::LOG_LEVEL_BRIEF_INFO:
                case self::LOG_LEVEL_VERBOSE_INFO:
                    fb::info($detail, $title);
                    break;

                case self::LOG_LEVEL_WARNING:
                    fb::warn($detail, $extendedTitle);
                    break;

                case self::LOG_LEVEL_ERROR:
                case self::LOG_LEVEL_CRITICAL:
//                    for ($i=0; $i < 3; $i++) {
//                        fb::log($backTrace[$i]['file'], '$backTrace[' . $i . '][file]');
//                        fb::log($backTrace[$i]['class'], '$backTrace[' . $i . '][class]');
//                        fb::log($backTrace[$i]['function'], '$backTrace[' . $i . '][function]');
//                        fb::log($backTrace[$i]['line'], '$backTrace[' . $i . '][line]');
//                    }

                    fb::error($detail, $title);

                    $errorLocation = $backTrace[1]['file'];
                    $errorLocation .= ': ' . $backTrace[2]['class'];
                    $errorLocation .= '->' . $backTrace[2]['function'];
                    $errorLocation .= ': Line ' . $backTrace[1]['line'];

                    fb::error($errorLocation);
                    break;
            }

        }
    }


    /**
     * Adds a critical error log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logCritical($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_CRITICAL);
    }


    /**
     * Adds a debug log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logDebug($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_DEBUG);
    }


    /**
     * Adds a error log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logError($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_ERROR);
    }


    /**
     * Starts a log group.
     *
     * @param string $name
     * The name of the group.
     *
     * @return NULL
     */
    protected function logGroup($name, $parameters = array())
    {
//        global $phpAnvil;

        fb::group($name, $parameters);
//        $phpAnvil->log->startGroup($name);
    }


    /**
     * Ends a log group.
     *
     * @param string $name
     * The name of the group.
     *
     * @return NULL
     */
    protected function logGroupEnd($name = '')
    {
//        global $phpAnvil;

        fb::groupEnd($name);

//        $phpAnvil->log->endGroup($name);
    }


    /**
     * Adds a brief info log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logInfo($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_BRIEF_INFO);
    }


    /**
     * Adds a verbose info log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logVerbose($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_VERBOSE_INFO);
    }


    /**
     * Adds a warning log entry to the log %router.
     *
     * @param string $detail
     * The full details of the log entry or a variable.
     * @param string $title
     * (optional) A short name describing the subject or title of the log
     * entry.
     *
     * @return NULL
     */
    protected function logWarning($detail, $title = '')
    {
        $this->log($detail, $title, self::LOG_LEVEL_WARNING);
    }


    /**
     * Disables all object logging by setting the log level to
     * @link{LOG_LEVEL_DISABLED}.
     *
     * @return NULL
     */
    public function disableLog()
    {
        $this->logLevel = self::LOG_LEVEL_DISABLED;
    }


    /**
     * Enables all object logging by setting the log level to the passed value
     * or default to @link{LOG_LEVEL_ALL}.
     *
     * @param integer $logLevel
     * The log level to enable the logging at.
     *
     * @return NULL
     */
    public function enableLog($logLevel = self::LOG_LEVEL_ALL)
    {
        $this->logLevel = $logLevel;
    }


    /**
     * Checks if object logging has been enabled.
     *
     * @return boolean
     *
     * @par
     * Returns TRUE if object logging has been enabled, or FALSE if object
     * logging has been disabled.
     */
    public function isLogEnabled()
    {
        return $this->logLevel > self::LOG_LEVEL_DISABLED;
    }

}

?>
