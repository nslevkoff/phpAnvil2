<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools anvilFuse
*/


require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';


/**
* Trace Info Static Class
*
* This class is used for gathering trace info lines from phpAnvil based objects.
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilFuse
*/
class anvilFuseTrace extends anvilObjectAbstract
{
	/**
	* Version number for this class release.
	*
	*/
	const VERSION = '1.0';


	#---- Private Properties
	private static $_trace = array();
	private static $_fullPathEnabled = false;
	private static $_startTime = 0;


	/**
	* Adds a trace info line.
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
	*/
	public static function add($file, $method, $line, $info, $type = self::TRACE_TYPE_DEBUG) {
		if (self::$_startTime == 0) {
			self::$_startTime = microtime(true);
		}
		$currentTime = microtime(true);
		$elapsedTime = number_format(($currentTime - self::$_startTime) * 100, 2, '.', '');

		if (self::$_fullPathEnabled) {
			array_push(self::$_trace, '[' . date('h:i:s A') . ', ' . $file . ', ' . $method . ', ' . $line . '] ' . $info);
		} else {
			array_push(self::$_trace, '[' . $elapsedTime . ',' . $type . ',' . basename($file) . ',' . $method . ',' . $line . '] ' . $info);
		}
	}


	/**
	* Disables full file paths used in trace info lines.
	*
	*/
	public static function disableFullPath() {
		self::$_fullPathEnabled = false;
	}


	/**
	* Enables full file paths used in trace info lines.
	*
	*/
	public static function enableFullPath() {
		self::$_fullPathEnabled = true;
	}


	/**
	* Returns the name for the trace info type value.
	*
	* @param $type
    *   An integer indicating the type of trace to get the name for.
	* @return
    *   Returns a string containing the name for the trace info type.
	*/
	public static function getTypeName($type) {
		switch($type) {
			case self::TYPE_TEST:
				$name = 'Test';
				break;
			case self::TYPE_INFO:
				$name = 'Info';
				break;
			case self::TYPE_DEBUG:
				$name = 'Debug';
				break;
			case self::TYPE_WARNING:
				$name = 'Warning';
				break;
			case self::TYPE_ERROR:
				$name = 'Error';
				break;
			case self::TYPE_CRITICAL:
				$name = 'Critical';
				break;
			default:
				$name = 'Other';
		}

		return $name;
	}


	/**
	* Returns whether full file paths are used in trace info lines.
	*
	* @return boolean True if full file paths are enabled.
	*/
	public static function isFullPath() {
		return self::$_fullPathEnabled;
	}


	/**
	* Returns all of the trace info lines as a line break formatted string.
	*
	* @return string Line break formatted string of all trace info lines.
	*/
	public static function render() {
		$string = '';
		for($x = 0; $x < sizeof(self::$_trace); $x++) {
			$string .= self::$_trace[$x] . "\n";
		}
		return $string;
	}


	/**
	* Returns all of the trace info lines as a HTML formatted string.
	*
	* @return string HTML formatted string of all trace info lines.
	*/
	public static function renderHTML() {
		$html = '';
		for($x = 0; $x < sizeof(self::$_trace); $x++) {
			$html .= self::$_trace[$x] . "<br>\n";
		}
		return $html;
	}


	/**
	* Starts the tracing session.
	*
	*/
	public static function start() {
		self::$_startTime = microtime(true);
	}


	/**
	* Returns the trace array.
	*
	* @return array Trace info array.
	*/
	public static function toArray() {
		return self::$_trace;
	}

}

?>