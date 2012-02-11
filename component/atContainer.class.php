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

require_once('atControl.abstract.php');
require_once('atCollection.class.php');


/**
* Container Control
*
* @version		1.0
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
*/
class atContainer extends atControlAbstract {

	const VERSION        = '1.0';


	/**
	* Collection of children controls within the container.
	* 
	* @var atCollection $controls
	*/
	public $controls = null;
	

	//---- Define Properties for Documentation and IDE Use
	
	/**
	* atTemplate to use for rendering inside the container.
	* 
	* @var atTemplate $innerTemplate
	*/
	public $innerTemplate;

	
	public function __construct($id = 0, $properties = null, $traceEnabled = false) {
//		$this->enableTrace();

		//---- Unset defined properties before defining virtual versions
		//---- of the properties
		unset($this->innerTemplate);

		
		//---- Define virtual properties.
		$this->addProperty('innerTemplate', '');

		
		$this->controls = new atCollection();

		parent::__construct($id, $properties, $traceEnabled);
	}


	public function addControl($control) {
		$this->controls->add($control);
	}


	protected function preRenderControl($control) {
	}


	public function renderControls() {
//		$this->logDebug('Executing...id_' . $this->id);
		$return = '';
		$_atTemplate = null;
		if (is_object($this->atTemplate)) {
//			$this->logDebug('clone atTemplate:id_' . $this->id);
			$_atTemplate = clone $this->atTemplate;
		}
		for($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
			$objControl = $this->controls->current();
//			$this->logDebug('Render Control:id_' . $objControl->id);
			$this->preRenderControl($objControl);
			if ($this->innerTemplate != '' && is_object($_atTemplate)) {

				$content = $objControl->render($this->atTemplate);
//				$this->logDebug('Assign InnerTemplate:id_' . $objControl->id);
//				$this->logDebug('Assign InnerTemplate:' . $content);
				$_atTemplate->assign('id_' . $objControl->id, $content);
			} else {
//				$this->logDebug('Render Control:id_' . $objControl->id);
				$return .= $objControl->render($this->atTemplate);

			}
		}
		if ($this->innerTemplate != '' && is_object($_atTemplate)) {
//			$this->logDebug('Render innerTemplate:' . $this->innerTemplate);
			$return .= $_atTemplate->render($this->innerTemplate);
//			$this->logDebug('Render inner:' . $return);
		}

//		$this->logDebug('return:' . $return);
		return $return;
	}

	public function renderContent() {
		$return = '';
		$return .= $this->renderControls();
		return $return;
	}


	public function renderPreClientScript() {
//		$this->logDebug('renderPreClientScript...id_' . $this->id);
		$return = '';
		if (!is_null($this->controls)) {
			for($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
				$objControl = $this->controls->current();
				$return .= $objControl->renderPreClientScript();
			}
		}
		return $return;
	}


	public function renderPostClientScript() {
//		$this->logDebug('renderPostClientScript...id_' . $this->id);
		$return = '';
		if (!is_null($this->controls)) {
			for($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
				$objControl = $this->controls->current();
				$return .= $objControl->renderPostClientScript();
			}
		}
		return $return;
	}

}

?>