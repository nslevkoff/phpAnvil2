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


require_once('atContainer.class.php');


/**
* Image Rotator Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atRotator extends atContainer {

	const VERSION        	= '1.0';

//	const TRANSITION_TYPE_FADE = 1;

//	protected $duration = array();
//	protected $inTransitionType = array();
//	protected $outTransitionType = array();


	public $durationMS;
	public $frameClass;
	public $transitionMS;
	public $cssPath;
	public $jsPath;
	

	public function __construct($id = 'atr', $durationMS = 5000, $transitionMS = 1000, $class = '', $frameClass = '', $properties = array(), $traceEnabled = false) {
		$this->enableTrace();

		unset($this->durationMS);
		unset($this->frameClass);
		unset($this->transitionMS);
		unset($this->cssPath);
		unset($this->jsPath);
		
		
		$this->addProperty('durationMS', 5000);
		$this->addProperty('frameClass', '');
		$this->addProperty('transitionMS', 1000);
		$this->addProperty('cssPath', '/includes/atUI.css');
		$this->addProperty('jsPath', '/js/atUI.js');

		parent::__construct($id, $properties, $traceEnabled);

		$this->durationMS = $durationMS;
		$this->transitionMS = $transitionMS;
		$this->class = $class;
		$this->frameClass = $frameClass;

	}


	public function renderContent() {
		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...id_' . $this->id);

		$return = '';

		$return .= '<div';
		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}
		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}
		$return .= '>';

		$return .= $this->renderControls();

//		$return .= '&nbsp;</div>';
		$return .= '</div>';

		$return .= '<script type="text/javascript">';
		$return .= 'atui_startRotator("' . $this->id . '", ' . $this->controls->count() . ', ' . $this->durationMS . ', ' . $this->transitionMS . ');';
		$return .= '</script>';

		return $return;
	}


	public function renderControls() {
		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...id_' . $this->id);

		$return = '';
		$controlNum = 1;
		for($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {

			$objControl = $this->controls->current();
			$this->preRenderControl($objControl);
			$return .= '<div id="' . $this->id . $controlNum . '" class="atRotator';
			if (!$this->frameClass) {
				$return .= ' ' . $this->frameClass;
			}
			$return .= '" style="display:none">' . $objControl->render() . '</div>';

			$controlNum++;
		}
		return $return;
	}


	public function renderPreClientScript() {
		$return = '';
		if (!isRendered('atUI.js')) {
			if ($this->cssPath) {
				$return .= '<link rel="Stylesheet" type="text/css" href="' . $this->cssPath . '" />' . "\n";
			}

			$return .= '<script type="text/javascript" src="' . $this->jsPath . '"></script>' . "\n";
			renderOnce('atUI.js');
		}
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