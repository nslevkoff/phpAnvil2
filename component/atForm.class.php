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
* Form Control
*
* @version		1.0
* @date			12/20/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atForm extends atContainer
{
	const VERSION        = '1.0.1';


	public $action;
	public $defaultButtonID;
	public $encType;
	public $method;
	public $headerEnabled;
	public $bodyEnabled;
	public $footerEnabled;
    public $target;


	public function __construct($id = '', $method = 'post', $action = '', $properties = array(), $traceEnabled = false)
	{
		unset($this->action);
		unset($this->defaultButtonID);
		unset($this->encType);
		unset($this->method);
		unset($this->headerEnabled);
		unset($this->bodyEnabled);
		unset($this->footerEnabled);
        unset($this->target);


		$this->addProperty('action', '');
		$this->addProperty('defaultButtonID', '');
		$this->addProperty('encType', '');
		$this->addProperty('method', 'post');
		$this->addProperty('headerEnabled', true);
		$this->addProperty('bodyEnabled', true);
		$this->addProperty('footerEnabled', true);
        $this->addProperty('target', '');

		$this->method = $method;

		if ($action) {
			$this->action = $action;
		} else {
/*			$this->action = basename($_SERVER["SCRIPT_NAME"]);*/ #-- setting default wasn't needed. ~David~
		}

		parent::__construct($id, $properties, $traceEnabled);
	}

	protected function preRenderControl($control) {
		if ($this->defaultButtonID && is_subclass_of($control, 'atFormControl')) {
			$control->defaultButtonID = $this->defaultButtonID;
		}
	}

	public function renderContent() {
		$return = '';
		if ($this->headerEnabled) {
			$return .= $this->renderHeader();
		}

		if ($this->bodyEnabled) {
			$return .= $this->renderControls();
//            if ($this->devTemplate != null) $return .= $this->devTemplate->render($this->innerTemplate);
		}

		if ($this->footerEnabled) {
			$return .= $this->renderFooter();
		}

		return $return;
	}

	public function renderHeader() {
		$return = '<form';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->method) {
			$return .= ' method="' . $this->method . '"';
		}
/*		if ($this->action) {*/ #-- Always add action to the form even if blank. ~David~
			$return .= ' action="' . $this->action . '"';
/*		}*/
		if ($this->encType) {
			$return .= ' enctype="' . $this->encType . '"';
		}

        if ($this->target) {
      			$return .= ' target="' . $this->target . '"';
      		}

		$return .= '><div';

		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

		$return .= '>';

		return $return;
	}

	public function renderFooter() {
		$return = '</div></form>';

		return $return;
	}

	public function renderPreClientScript() {
		$return = '';
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