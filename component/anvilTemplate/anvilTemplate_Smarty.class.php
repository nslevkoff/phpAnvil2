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


require_once('anvilTemplate.abstract.php');


/**
* Smarty Template Class
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilTemplate_Smarty extends anvilTemplateAbstract {

	const VERSION        = '1.0';

	protected $_isConstructed = false;


	public function __construct($enginePath, $cachePath, $themeRootPath, $theme, $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		parent::__construct($enginePath, $cachePath, $themeRootPath, $theme, $properties, $traceEnabled);

		require_once($this->enginePath . '/Smarty.class.php');
		$this->engine = new Smarty;

		$this->engine->compile_dir = $this->cachePath . '/templates_c/';
		$this->engine->config_dir = $this->cachePath . '/configs/';
		$this->engine->cache_dir = $this->cachePath . '/cache/';
		$this->engine->template_dir = realpath($this->themeRootPath) . '/' . $this->theme . '/';

		$this->_isConstructed = true;
	}

	public function __set($propertyName, $value) {
		switch ($propertyName) {
			case 'theme':
				$this->theme = $value;

				if ($this->_isConstructed) {
					$this->engine->template_dir = realpath($this->themeRootPath) . '/' . $this->theme . '/';
					$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Template Directory Now Set To ' . $this->engine->template_dir);
				}
				break;

			default:
				parent::__set($propertyName, $value);
		}
	}

	public function assign($var, $value) {
		$this->engine->assign($var, $value);
	}

	public function display($template) {
		$this->engine->display($template);
	}

	public function render($template) {
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'template_dir = ' . $this->engine->template_dir, DevTrace::TYPE_DEBUG);
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'compile_dir = ' . $this->engine->compile_dir, DevTrace::TYPE_DEBUG);
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'config_dir = ' . $this->engine->config_dir, DevTrace::TYPE_DEBUG);
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'cache_dir = ' . $this->engine->cache_dir, DevTrace::TYPE_DEBUG);

		return $this->engine->fetch($template);
	}

	public function reset() {
		$this->engine->clear_all_assign();
	}
}

?>
