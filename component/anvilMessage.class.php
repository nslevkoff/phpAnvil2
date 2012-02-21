<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/


require_once('anvilControl.abstract.php');


/**
* Message Control
*
* @version		1.1
* @date			3/9/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilMessage extends anvilControlAbstract
{
	const VERSION       = '1.1';


	public $sessionName = 'dc_message';
	public $separator = "<br />\n";
	public $icon;
	public $singleBox = true;


	public function __construct($id = 'message', $sessionName = 'dc_message', $class = 'message', $separator = "<br />\n", $properties = null) {
//		$this->_traceEnabled = $traceEnabled;

//		unset($this->sessionName);
//		unset($this->separator);
//		unset($this->icon);
//		unset($this->singleBox);


//		$this->addProperty('sessionName', 'dc_message');
//		$this->addProperty('separator', "<br />\n");
//		$this->addProperty('icon', '');
//		$this->addProperty('singleBox', true);

		parent::__construct($id, $properties);

		$this->sessionName = $sessionName;
		$this->class = $class;
	}


	public function __get($propertyName) {
		$return = '';
		switch ($propertyName) {
			case 'message':
				if (isset($_SESSION)) {
					if (array_key_exists($this->sessionName, $_SESSION))
                    {
//                        FB::warn('Deleting message...');

						$return = $_SESSION[$this->sessionName];
						$this->delete();
					}
				} else {
					$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'No Session Found - Unable to get message.', DevTrace::TYPE_WARNING);
				}

				break;

			default:
				$return = parent::__get($propertyName);
		}

		return $return;
	}


	public function __set($propertyName, $value) {

		$return = '';

		$return = parent::__set($propertyName, $value);

		switch ($propertyName) {
			case 'singleBox':
				if (!$this->singleBox) {
					$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Setting multiple box mode, changing separator...');

					$this->separator = '|||';
				}
				break;

		}

		return $return;
	}


	public function add($message) {
		if (isset($_SESSION)) {

			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Adding [' . $this->id . '] Message: ' . $message);

			if (array_key_exists($this->sessionName, $_SESSION)) {
				$_SESSION[$this->sessionName] .= $message . $this->separator;
			} else {
				$_SESSION[$this->sessionName] = $message . $this->separator;
			}

//            FB::log($_SESSION[$this->sessionName], '$_SESSION[$this->sessionName]');
			return true;

		} else {
			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'No Session Found - Unable to add message.', DevTrace::TYPE_WARNING);
		}
	}


	public function delete() {
		if (isset($_SESSION)) {
			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Deleting message...');
			unset($_SESSION[$this->sessionName]);
			return true;

		} else {
			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'No Session Found - Unable to delete message.', DevTrace::TYPE_WARNING);
			return false;
		}
	}


	public function renderContent() {
		global $firePHP;


		$return = '';
		$message = nl2br($this->message);

		if (!empty($message)) {

//        $_SESSION[$this->sessionName]--;

//			if ($this->id) {
//				$return .= '<div id="' . $this->id . '"';
//				if ($this->class) {
//					$return .= ' class="' . $this->class . '"';
//				}
//				$return .= '>';
//			}

			if ($this->singleBox) {
				$return .= '<div';

				if ($this->class) {
					$return .= ' class="';
					if ($this->id) {
						$return .= $this->id . ' ';
					}
					$return .= $this->class . '"';
				}
				$return .= '>';

				$return .= '>';


				if ($this->icon) {
					$return .= '<div class="image"><img src="' . $this->icon . '" alt="' . $message . '"/></div>';
				}

				$return .= '<div class="info">' . $message . '</div>';
				$return .= '</div>';
			} else {
				$messages =  explode($this->separator, $message);

				for($i = 0; $i < sizeof($messages); ++$i) {
					$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Rendering message #' . $i . '...');


					if (!empty($messages[$i])) {
						$return .= '<div';
						if ($this->class) {
							$return .= ' class="';
							if ($this->id) {
								$return .= $this->id . ' ';
							}
							$return .= $this->class . '"';
						}
						$return .= '>';


						if ($this->icon) {
							$return .= '<div class="image"><img src="' . $this->icon . '" alt="' . $messages[$i] . '"/></div>';
						}

						$return .= '<div class="info">' . $messages[$i] . '</div>';
						$return .= '</div>';
					}
				}
			}

//			if ($this->id) {
//				$return .= '</div>';
//			}

//        FB::log($return, 'anvilMessage->RenderContent()');

        }

//		$firePHP->_log($return);


		return $return;
	}


//	public function renderPreClientScript() {
//		$return = '';
//		$return .= parent::renderPreClientScript();
//		return $return;
//	}


	public function renderPreClientScript() {
		$return = '';

		$return .= '<script type="text/javascript">' . "\n";
		$return .= "\t" . '$(document).ready(function(){' . "\n";
//		$return .= "\t\t" . '$("div:hidden:' . $this->id . '").fadeIn("slow");' . "\n";
//		$return .= "\t\t" . '$("div#' . $this->id . '").fadeIn("slow");' . "\n";
		$return .= "\t\t" . '$("div.' . $this->id . '").fadeOut(1000);' . "\n";
		$return .= "\t\t" . '$("div.' . $this->id . '").fadeIn(1000);' . "\n";
//		$return .= "\t\t" . '$("div#' . $this->id . '").fadeTo("slow", 0.33);' . "\n";
//		$return .= "\t\t" . '$("div#' . $this->id . '").fadeOut("slow");' . "\n";
//		$return .= "\t\t" . '$("div#' . $this->id . '").fadeIn("slow");' . "\n";
		$return .= "\t" . '});' . "\n";
		$return .= '</script>' . "\n";

		$return .= parent::renderPreClientScript();
		return $return;
	}


	public function renderPostClientScript() {
		$return = '';
		$return .= parent::renderPostClientScript();
		return $return;
	}
}

?>