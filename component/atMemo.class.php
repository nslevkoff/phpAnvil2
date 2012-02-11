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


require_once('atFormControl.abstract.php');


/**
* Multi-Lined Text Entry Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atMemo extends atFormControlAbstract {

	const VERSION        = '1.0';


	public $columns;
	public $rows;
	public $value;
    public $wrapEnabled = false;


	public function __construct($id = '', $name = '', $columns = 50, $rows = 3, $value = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

		unset($this->columns);
		unset($this->rows);
		unset($this->value);
        unset($this->wrapEnabled);


//		$this->addProperty('maxFileSize', 102400);
		$this->addProperty('columns', 50);
		$this->addProperty('rows', 3);
		$this->addProperty('value', '');
        $this->addProperty('wrapEnabled', false);
        $this->addProperty('wrapClass', 'memoWrap');

		$this->columns = $columns;
		$this->rows = $rows;
		$this->value = $value;

		parent::__construct($id, $name, $properties, $traceEnabled);
	}


	public function renderContent()
    {
        $return = '';

        if ($this->wrapEnabled) {
            $return .= '<p class="' . $this->wrapClass . '">';
        }

        $return .= $this->renderLabel();

		$return .= '<textarea';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

		if ($this->columns) {
			$return .= ' cols="' . $this->columns . '"';
		}

		if ($this->rows) {
			$return .= ' rows="' . $this->rows . '"';
		}

        if ($this->class) {
            $return .= ' class="' . $this->class . '"';
        }

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

		$return .= '>';

		if ($this->value) {
			$return .= $this->value;
		}

		$return .= '</textarea>';

        if ($this->wrapEnabled) {
            $return .= '</p>';
        }

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