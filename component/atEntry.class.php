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
* Text Entry Control
*
* @version		1.0.2
* @date			12/21/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atEntry extends atFormControlAbstract {

	const VERSION        = '1.0.2';

	const TYPE_NORMAL	= 1;
	const TYPE_PASSWORD	= 2;
	const TYPE_FILE		= 3;

    public $disabled = false;
    public $onKeyPress;
    public $wrapEnabled = false;


	public function __construct($id = '', $name = 'btn', $size = '', $maxLength = '', $value = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

        $this->enableLog();


        unset($this->disabled);
        unset($this->wrapEnabled);

//		$this->addProperty('maxFileSize', 102400);
		$this->addProperty('maxLength', '');
		$this->addProperty('size', '');
		$this->addProperty('type', self::TYPE_NORMAL);
		$this->addProperty('value', '');
        $this->addProperty('disabled', false);
        $this->addProperty('wrapEnabled', false);
        $this->addProperty('wrapClass', 'inputWrap');

		parent::__construct($id, $name, $properties, $traceEnabled);

		$this->size = $size;
		$this->maxLength = $maxLength;
		$this->value = $value;

//        $this->logdebug('|' . $value . '|', $this->name . '=');
	}

	public function renderContent()
    {

        $return = '';
        
        if ($this->wrapEnabled) {
            $return .= '<p class="' . $this->wrapClass . '">';
        }
        
        $return .= $this->renderLabel();

		$return .= '<input type="';

		switch ($this->type) {
			case self::TYPE_PASSWORD:
				$return .= 'password';
				break;
			case self::TYPE_FILE:
				$return .= 'file';
				break;
			case self::TYPE_NORMAL:
			default:
				$return .= 'text';
				break;
		}
		$return .= '"';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

		if ($this->size) {
			$return .= ' size="' . $this->size . '"';
		}

		if ($this->maxLength) {
			$return .= ' maxlength="' . $this->maxLength . '"';
		}

        if ($this->class) {
            $return .= ' class="' . $this->class . '"';
        }

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

//		$this->enableTrace();

//        $this->logdebug('|' . $this->value . '|', $this->name . '=');

//		if (!empty($this->value) || ($this->value == 0 && !is_null($this->value))) {
        if ($this->value != '' || ($this->value == 0 && !is_null($this->value))) {
//			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $this->name . ' = SUCCESS!', DevTrace::TYPE_DEBUG);
			$return .= ' value="' . $this->value . '"';
		} else {
//			$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, $this->name . ' = FAILED! (' . $this->value . ')', DevTrace::TYPE_DEBUG);
		}

        if (!empty($this->onKeyPress))
        {
            $return .= ' onkeypress="' . $this->onKeyPress . '"';
        } else if ($this->defaultButtonID) {
			$return .= ' onkeypress="enterSubmit(event, \'' . $this->defaultButtonID . '\');"';
		}

		/*
		if (isset($this->_onChange)) {
			$return .= ' onChange="' . $this->_onChange . '"';
		}
		*/

        if ($this->disabled) {
            $return .= ' disabled="disabled"';
        }


		$return .= ' />';

        if ($this->wrapEnabled) {
            $return .= '</p>';
        }

//		if ($this->_type == self::TYPE_FILE) {
//			$return .= '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->_maxFileSize . '">';
//		}

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