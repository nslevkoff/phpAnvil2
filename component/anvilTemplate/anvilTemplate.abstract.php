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


require_once PHPANVIL2_COMPONENT_PATH . 'anvilDynamicObject.abstract.php';


/**
* Template Class
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class anvilTemplateAbstract extends anvilDynamicObjectAbstract {

	const VERSION        = '1.0';

	
	public $cachePath;
	public $engine;
	public $enginePath;
	public $theme;
	public $themeRootPath;
	

	public function __construct($enginePath, $cachePath, $themeRootPath, $theme, $properties = array()) {
//		$this->_traceEnabled = $traceEnabled;


//		$this->addProperty('cachePath', '');
//		$this->addProperty('engine', '');
//		$this->addProperty('enginePath', '');
//		$this->addProperty('theme', '');
//		$this->addProperty('themeRootPath', '');

		$this->enginePath = $enginePath;
		$this->cachePath = $cachePath;
		$this->themeRootPath = $themeRootPath;
		$this->theme = $theme;

		$this->importProperties($properties);

		parent::__construct();

	}

	public function __get($propertyName) {
		switch ($propertyName) {
			case 'themePath':
				$return = $this->themeRootPath . '/' . $theme;
				break;

			default:
				$return = parent::__get($propertyName);
		}

		return $return;
	}

	public function assign($var, $value) {
	}

	public function display($template) {
        return true;
	}

	public function render($template) {
	}

	public function reset() {
	}
}

?>