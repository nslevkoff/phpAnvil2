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
* Image Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilImage extends anvilControlAbstract {

	const VERSION        = '1.0';


	public $alt;
    public $title;
	public $image;
	public $height;
	public $width;


	public function __construct($id = '', $image = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

//		unset($this->alt);
//        unset($this->title);
//		unset($this->image);
//		unset($this->height);
//		unset($this->width);


//		$this->addProperty('alt', '');
//        $this->addProperty('title', '');
//		$this->addProperty('image', '');
//		$this->addProperty('height', '');
//		$this->addProperty('width', '');

		$this->image = $image;

		parent::__construct($id, $properties, $traceEnabled);
	}

	public function renderContent() {

		$return = '';

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

        if ($this->title) {
            $return .= ' title="' . $this->title . '"';
//        } else {
//            $return .= ' title="' . $this->image . '"';
        }

		if ($this->class) {
			$return .= ' class="' . $this->class . '"';
		}

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

		if ($this->height) {
			$return .= ' height="' . $this->height . '"';
		}

		if ($this->width) {
			$return .= ' width="' . $this->width . '"';
		}

		$return .= ' />';

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