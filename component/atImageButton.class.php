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


require_once('atButton.class.php');


/**
* Image Button Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atImageButton extends atButton {

	const VERSION        = '1.0';

	
	public $alt;
	public $image;
	public $height;
	public $hoverImage;
	public $width;
	public $targetID;
	

	public function __construct($id = '', $image = '', $hoverImage = '', $targetID = '', $value = '', $properties = array(), $traceEnabled = false) {
		$this->enableTrace();

		unset($this->alt);
		unset($this->image);
		unset($this->height);
		unset($this->hoverImage);
		unset($this->width);
		unset($this->targetID);
		
		
		$this->addProperty('alt', '');
		$this->addProperty('image', '');
		$this->addProperty('height', '');
		$this->addProperty('hoverImage', '');
		$this->addProperty('width', '');
		$this->addProperty('targetID', '');

		$this->image = $image;
		$this->hoverImage = $hoverImage;
		$this->targetID = $targetID;

		parent::__construct($id, null, self::TYPE_IMAGE, $image, $properties, $traceEnabled);

		$this->value = $value;
	}


	public function renderContent() {

		$return = '';

			$return .= '<a';

			if ($this->id) {
				$return .= ' id="' . $this->id . '"';
			}

			if ($this->class) {
				$return .= ' class="' . $this->class . '"';
			}

			if ($this->targetID) {
				if ($this->confirmMsg) {
					$return .= ' onclick="if (confirm(\'' . $this->confirmMsg . '\')) { document.getElementById(\'' . $this->targetID . '\').value=\'' . $this->value . '\';document.getElementById(\'' . $this->targetID . '\').form.submit(); }"';
				} else {
					$return .= ' onclick="document.getElementById(\'' . $this->targetID . '\').value=\'' . $this->value . '\';document.getElementById(\'' . $this->targetID . '\').form.submit();"';
				}
			} elseif ($this->confirmMsg) {
				$return .= " onclick=\"return confirm(" . $this->confirmMsg . ");\"";
			}

			$return .= ' style="cursor:pointer;"';
//			$return .= ' href="#"';

			$return .= '>';
//		}

		$return .= '<img';

		if ($this->id) {
			$return .= ' id="' . $this->id . 'i"';
		}

//		if ($this->name) {
//			$return .= ' name="' . $this->name . '"';
//		}

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

//			if ($this->targetID) {
//				$return .= ' onclick="' . $this->targetID . '.value=\'' . $this->value . '\';' . $this->targetID . '.form.submit();"';
//			}

//		if ($this->confirmMsg) {
//			$return .= " onclick=\"return confirm(" . $this->confirmMsg . ");\"";
//		}

		$return .= ' />';

//		if ($this->linkURL) {
			$return .= '</a>';
//		}

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