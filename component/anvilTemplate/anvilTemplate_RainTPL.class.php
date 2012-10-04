<?php
require_once('anvilTemplate.abstract.php');


/**
* Rain TPL Template Class
*
* @property RainTPL $engine
*/
class anvilTemplate_RainTPL extends anvilTemplateAbstract {

	const VERSION        = '1.0';

	protected $_isConstructed = false;


	public function __construct($enginePath, $cachePath, $themeRootPath, $theme, $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		parent::__construct($enginePath, $cachePath, $themeRootPath, $theme, $properties, $traceEnabled);

        $this->enableLog();

		require_once($this->enginePath . '/rain.tpl.class.php');

		$this->engine = new RainTPL();

//		$this->engine->compile_dir = $this->cachePath . '/templates_c/';
//		$this->engine->config_dir = $this->cachePath . '/configs/';
		RainTPL::$cache_dir = $this->cachePath . '/cache/';
		RainTPL::$tpl_dir = realpath($this->themeRootPath) . '/' . $this->theme . '/';
        RainTPL::$tpl_ext = 'tpl';

        $this->_logVerbose(RainTPL::$cache_dir, 'Rain.TPL Cache Directory');
        $this->_logVerbose(RainTPL::$tpl_dir, 'Rain.TPL Template Directory');

		$this->_isConstructed = true;
	}

	public function __set($propertyName, $value) {
		switch ($propertyName) {
			case 'theme':
				$this->theme = $value;

				if ($this->_isConstructed) {
					RainTPL::$tpl_dir = realpath($this->themeRootPath) . '/' . $this->theme . '/';
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
		$this->engine->draw($template);
	}


    /**
     * @param string $template
     *
     * @return string
     */
	public function render($template) {
		return $this->engine->draw($template, $return_string = true);
	}

	public function reset() {
		$this->engine->var = NULL;
        $this->engine->var = array();
    }
}

?>
