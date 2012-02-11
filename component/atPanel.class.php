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
* Panel Container Control
*
* @version		1.2
* @date			10/12/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2009-2011 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atPanel extends atContainer {

	const VERSION = '1.2';

	public $title;
	private $_content;
	private $_isCollapsable = false;
	private $_isCollapsed = false;
	private $_isFooterEnabled = false;
    public $tag = 'div';

	private $_mainClass = 'panel';


	public function __construct($id = 0, $mainClass = 'panel', $properties = null, $traceEnabled = false) {
		$this->_mainClass = $mainClass;

		parent::__construct($id, $properties, $traceEnabled);
	}

	public function renderContent() {
		$return = '<' . $this->tag;

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		$return .= ' class="' . $this->_mainClass . '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        if ($this->title) {
            $return .= ' title="' . $this->title . '"';
        }

        $return .= '>';

		$return .= $this->renderControls();

		$return .= '</' . $this->tag . ">\n";

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