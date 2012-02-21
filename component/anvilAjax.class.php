<?php
/**
* @file
* phpAnvilTools Ajax Classes
*
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools anvilAjax
*/


require_once('anvilObject.abstract.php');

/**
* Primary Ajax Class
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilAjax
*/
class anvilAjax extends anvilObjectAbstract {
	/**
	* Version number for this class release.
	*
	*/
	const VERSION		= '1.0';

	const RESPONSE_ACTION_COMBOBOX 	= 1;
	const RESPONSE_ACTION_DIV		= 2;


	public $jsPath;
	private $_response = array();


	public function __construct($jsPath = 'js/') {
		$this->jsPath = $jsPath;

		$this->_response['packet'] = array();
	}


	public function addResponse($sourceID, $actionID, $targetID, $data) {
		$newResponse = new anvilAjax_Response($sourceID, $actionID, $targetID, $data);
		array_push($this->_response['packet'], $newResponse);
	}


	private function json_real_encode($obj){
		$f = $r = array();
		foreach(array_merge(range(0, 7), array(11), range(14, 31)) as $v) {
			$f[] = chr($v);
			$r[] = "\\u00".sprintf("%02x", $v);
		}
		return str_replace($f, $r, json_encode($obj));
	}


	public function send() {

		$packet = $this->json_real_encode($this->_response);

		if(is_array($packet) || is_object($packet)) {
			$packet = json_encode($packet);
			$packet = str_replace('\"', '"', $packet);
		}

		echo($packet);

		return true;
	}


	public function renderHTML () {
		global $ata_url;

		$html = "<script type=\"text/javascript\">\n";
		$html .= "\t<!--\n";
		$html .= "\t\tvar this_url = \"" . $ata_url . "\";\n";
		$html .= "\t-->\n";
		$html .= "\t</script>\n";
		$html .= "\t<script type=\"text/javascript\" src=\"" . $this->jsPath . "JSON.js\"></script>\n";
		$html .= "\t<script type=\"text/javascript\" src=\"" . $this->jsPath . "anvilAjax.js\"></script>\n";

		return $html;
	}
}


/**
* Ajax Request Model Class
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilAjax
*/
class anvilAjax_Request {
	public $sourceID;
	public $moduleID;
	public $moduleActionID;
	public $responseActionID;
	public $responseTargetID;
	public $data;
}


/**
* Ajax Response Model Class
*
* @version		1.0
* @date			8/25/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools anvilAjax
*/
class anvilAjax_Response {
	public $sourceID;
	public $actionID;
	public $targetID;
	public $data;

	public function __construct($sourceID, $actionID, $targetID, $data) {
		$this->sourceID = $sourceID;
		$this->actionID	= $actionID;
		$this->targetID = $targetID;
		$this->data = $data;
	}
}


?>