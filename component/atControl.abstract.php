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


require_once('atDynamicObject.abstract.php');


static $alreadyRendered = array();

function isRendered($key) {
	global $alreadyRendered;

	return array_key_exists($key, $alreadyRendered);
}


function renderOnce($key) {
	global $alreadyRendered;

	$alreadyRendered[$key] = true;
}


/**
* Base Control Object Class
*
* @version		1.0
* @date			8/24/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class atControlAbstract extends atDynamicObjectAbstract
{
	const VERSION        	= '1.0';

	protected $_triggers = array();

	//---- Define Properties for Documentation and IDE Use

	/**
	* CSS Class Name
	*
	* @var string $class
	*/
	public $class;

	/**
	* HTML ID to use for the rendered control.
	*
	* @var string $id
	*/
	public $id;

	/**
	* Custom CSS style for the rendered control.
	*
	* @var string $style
	*/
	public $style;

	/**
	* Javascript to render at the top of the page output.
	*
	* @var string $preClientScript
	*/
	public $preClientScript;

	/**
	* Javascript to render at the bottom of the page output.
	*
	* @var string $postClientScript
	*/
	public $postClientScript;

	/**
	* atTemplate object for rendering the control.
	*
	* @var atTemplate $atTemplate
	*/
	public $atTemplate;

	/**
	* Outer template filename to use for rendering the control inside of.
	*
	* @var string $outerTemplate
	*/
	public $outerTemplate;

	/**
	* Variable ID to use for rendering the control inside the outer template.
	*
	* @var string $outerTemplateID
	*/
	public $outerTemplateID;


	public function __construct($id = 0,
								$properties = array(),
								$traceEnabled = false)
	{
//		$this->enableTrace();

		//---- Unset defined properties before defining virtual versions
		//---- of the properties
		unset($this->class);
		unset($this->id);
		unset($this->style);
		unset($this->preClientScript);
		unset($this->postClientScript);
		unset($this->atTemplate);
		unset($this->outerTemplate);
		unset($this->outerTemplateID);


		//---- Define virtual properties.
		$this->addProperty('class', '');
		$this->addProperty('id', 0);
		$this->addProperty('style', '');

		$this->addProperty('preClientScript', '');
		$this->addProperty('postClientScript', '');

		$this->addProperty('atTemplate', null);
		$this->addProperty('outerTemplate', '');
		$this->addProperty('outerTemplateID', '');

		$this->id = $id;
		$this->importProperties($properties);

		parent::__construct($traceEnabled);

//        $this->enableLog();
	}


	public function addTrigger($event, $code)
	{
        $this->logDebug('Adding trigger...');
        $this->logDebug($event, '$event');
        $this->logDebug($code, '$code');


		$this->_triggers[$event][] = $code;

        $this->logDebug($this->_triggers, '$this->_triggers');
	}


	public function render($atTemplate = null)
	{
		$return = '';
		if (is_object($atTemplate)) {
			$this->atTemplate = $atTemplate;
		}
		if ($this->outerTemplate != '' && is_object($atTemplate)) {
			$return .= $this->renderTemplate();
		} else {
			$return .= $this->renderContent();
		}
		return $return;
	}


	public function renderContent()
	{
		return '';
	}


	public function renderTemplate()
	{
		$return = '';
		if (is_object($this->atTemplate)) {
			$_atTemplate = clone $this->atTemplate;
		}
		if (is_object($_atTemplate)) {
			if ($this->outerTemplateID != '') {
				$_atTemplate->assign($this->outerTemplateID, $this->renderContent());
			} else {
				$_atTemplate->assign('atControl', $this->renderContent());
			}
			$return .= $_atTemplate->render($this->outerTemplate);
		}

		return $return;
	}


	public function renderTriggers()
	{
		$return = '';

        $this->logDebug('Rendering triggers...');

        $this->logDebug($this->_triggers, '$this->_triggers');

		foreach(array_keys($this->_triggers) as $event)
		{
			$return .= ' ' . $event . '="' . $this->id . '_' . $event . '(this);"';
		}

//        fb::log($return, 'Triggers');
        $this->logDebug($return, 'Triggers');

		return $return;
	}


	public function renderPreClientScript()
	{
		$return = '';

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'rendering...', self::TRACE_TYPE_DEBUG);

		if (count($this->_triggers) > 0) {
			$return .= '<script  type="text/javascript">' . "\n";

			foreach(array_keys($this->_triggers) as $event)
			{
				$return .= 'function ' . $this->id . '_' . $event . '(object) {' . "\n";

				foreach(array_values($this->_triggers[$event]) as $code)
				{
					$return .= "\t" . $code . "\n";
				}

				$return .= '}' . "\n";
			}

			$return .= "</script>\n";
		}

		return $return;
	}


	public function renderPostClientScript()
	{
		return '';
	}

}

?>