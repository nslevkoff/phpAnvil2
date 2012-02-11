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


require_once('atImage.class.php');


/**
* Image Button Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atImageLink extends atImage {

	const VERSION        = '1.0';

	
	public $hoverImage;
	public $linkURL;
	public $linkTarget;
	

	public function __construct($id = '', $linkURL = '', $image = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;
		$this->enableTrace();

		unset($this->hoverImage);
		unset($this->linkURL);
		unset($this->linkTarget);
		
		
		$this->addProperty('hoverImage', '');
		$this->addProperty('linkURL', $linkURL);
		$this->addProperty('linkTarget', '');

		parent::__construct($id, $image, $properties, $traceEnabled);
	}


	public function renderContent() {

		$return = '';

		if ($this->linkURL) {
			$return .= '<a href="' . $this->linkURL . '"';

			if ($this->class) {
				$return .= ' class="' . $this->class . '"';
			}

			if ($this->linkTarget) {
				$return .= ' target="' . $this->linkTarget . '"';
			}
			$return .= '>';
		}
//        parent::renderContent();
		$return .= '<img';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		$return .= ' src="' . $this->image . '"';

		if ($this->alt) {
			$return .= ' alt="' . $this->alt . '"';
		} else {
			$return .= ' alt="' . $this->image . '"';
		}

		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}

		if ($this->height) {
			$return .= ' height="' . $this->height . '"';
		}

		if ($this->width) {
			$return .= ' width="' . $this->width . '"';
		}

		$return .= ' />';

		if ($this->linkURL) {
			$return .= '</a>';
		}

		$return .= "\n";

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