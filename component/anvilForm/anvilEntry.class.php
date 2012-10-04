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


require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';
require_once 'anvilFormControl.abstract.php';


/**
* Text Entry Control
*
* @version		1.0.2
* @date			12/21/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilEntry extends anvilFormControlAbstract {

	const VERSION        = '1.0.2';

    private $_sizeClass = array(
        'spanAuto',
        'input-mini',
        'input-small',
        'input-medium',
        'input-large',
        'input-xlarge',
        'input-xxlarge',
        'span1',
        'span2',
        'span3',
        'span4',
        'span5',
        'span6',
        'span7',
        'span8',
        'span9',
        'span10',
        'span11',
        'span12'
    );

    const SIZE_AUTO = 0;
    const SIZE_MINI = 1;
    const SIZE_SMALL = 2;
    const SIZE_MEDIUM = 3;
    const SIZE_LARGE = 4;
    const SIZE_XLARGE = 5;
    const SIZE_XXLARGE = 6;
    const SIZE_SPAN1 = 7;
    const SIZE_SPAN2 = 8;
    const SIZE_SPAN3 = 9;
    const SIZE_SPAN4 = 10;
    const SIZE_SPAN5 = 11;
    const SIZE_SPAN6 = 12;
    const SIZE_SPAN7 = 13;
    const SIZE_SPAN8 = 14;
    const SIZE_SPAN9 = 15;
    const SIZE_SPAN10 = 16;
    const SIZE_SPAN11 = 17;
    const SIZE_SPAN12 = 18;


    const TYPE_NORMAL	= 1;
	const TYPE_PASSWORD	= 2;
	const TYPE_FILE		= 3;

    /** @var anvilContainer */
    public $append;

    /** @var anvilContainer */
    public $prepend;

    public $appendText;
    public $disabled = false;
    public $onKeyPress;
    public $length;
    public $maxLength;
    public $prependText;
    public $readOnly = false;
    public $size;
    public $type = self::TYPE_NORMAL;
    public $value;


    public $wrapEnabled = false;
    public $wrapClass = 'inputWrap';

    public $placeholder;

    //---- Validation Properties
    public $validation = true;
    public $required = false;


	public function __construct($id = '', $name = 'unknown', $size = self::SIZE_MEDIUM, $value = '', $properties = null) {
//		$this->_traceEnabled = $traceEnabled;

        $this->enableLog();


//        unset($this->disabled);
//        unset($this->wrapEnabled);

//		$this->addProperty('maxFileSize', 102400);
//		$this->addProperty('maxLength', '');
//		$this->addProperty('size', '');
//		$this->addProperty('type', self::TYPE_NORMAL);
//		$this->addProperty('value', '');
//        $this->addProperty('disabled', false);
//        $this->addProperty('wrapEnabled', false);
//        $this->addProperty('wrapClass', 'inputWrap');

		parent::__construct($id, $name, $properties);

        $this->prepend = new anvilContainer();
        $this->append  = new anvilContainer();

        $this->size = $size;
//        $this->type = $type;
//		$this->maxLength = $maxLength;
		$this->value = $value;

//        $this->_logdebug('|' . $value . '|', $this->name . '=');
	}

	public function renderContent()
    {

        $return = '';

        $appendHTML = $this->append->renderContent();
        $prependHTML = $this->prepend->renderContent();

//        if ($this->wrapEnabled) {
//            $return .= '<p class="' . $this->wrapClass . '">';
//        }
        
//        $return .= $this->renderLabel();

        //---- Render Prepend or Start Append Wrapper --------------------------
        if (!empty($prependHTML) || !empty($this->prependText) || !empty($appendHTML) || !empty($this->appendText)) {

//            if (!empty($this->appendText)) {
//                $return .= '<div class="input-append">';
//            } elseif (!empty($this->prependText)) {
//                $return .= '<div class="input-prepend">';
//                $return .= '<span class="add-on">' . $this->prependText . '</span>';
//            }

                $return .= '<div class="';
            if (!empty($appendHTML) || !empty($this->appendText)) {
                $return .= ' input-append';
            }

            if (!empty($prependHTML) || !empty($this->prependText)) {
                $return .= ' input-prepend';
            }
            $return .= '">';

            $return .= $prependHTML;

            if (!empty($this->prependText)) {
                $return .= '<span class="add-on">' . $this->prependText . '</span>';
            }
        }


        //---- Render INPUT Tag ------------------------------------------------
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

		if ($this->length) {
			$return .= ' size="' . $this->length . '"';
		}

		if ($this->maxLength) {
			$return .= ' maxlength="' . $this->maxLength . '"';
		}

        if ($this->readOnly) {
            $return .= ' readonly="readonly"';
        }

        $return .= ' class="';

//        if ($this->size != self::SIZE_LENGTH) {
            $return .= $this->_sizeClass[$this->size];
//        }

        if ($this->class) {
            $return .= ' ' . $this->class;
        }

        if ($this->validation) {
            $return .= ' anvil-validation';

            if ($this->required) {
                $return .= ' required';
            }
        }


            $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

//		$this->enableTrace();

//        $this->_logdebug('|' . $this->value . '|', $this->name . '=');

//		if (!empty($this->value) || ($this->value == 0 && !is_null($this->value))) {
        if ($this->value != '' || ($this->value == 0 && !is_null($this->value))) {
//			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $this->name . ' = SUCCESS!', DevTrace::TYPE_DEBUG);
			$return .= ' value="' . $this->value . '"';
		} else {
//			$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $this->name . ' = FAILED! (' . $this->value . ')', DevTrace::TYPE_DEBUG);
		}

//        if ($this->validation && $this->required) {
//            $return .= ' onchange="validateRequired();"';
//        }

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

        if ($this->placeholder) {
            $return .= ' placeholder="' . $this->placeholder . '"';
        }

		$return .= ' />';


        //---- Render Append or Close Prepend Wrapper --------------------------
//        if (!empty($this->prependText) || !empty($this->appendText)) {
        if (!empty($prependHTML) || !empty($this->prependText) || !empty($appendHTML) || !empty($this->appendText)) {
            if (!empty($this->appendText)) {
                $return .= '<span class="add-on">' . $this->appendText . '</span>';
            }

            $return .= $appendHTML;

//            if (!empty($this->appendText)) {
//                $return .= '<span class="add-on">' . $this->appendText . '</span>';
//                $return .= '</div>';
//            } elseif (!empty($this->prependText)) {
                $return .= '</div>';
//            }
        }

        //---- Render Validation Placeholder -----------------------------------
        if ($this->validation && $this->required) {
            $return .= '<span class="help-validation">';
            $return .= '<span class="label"></span>';
            $return .= '<span class="description"></span>';
            $return .= '</span>';
        }

//        if ($this->wrapEnabled) {
//            $return .= '</p>';
//        }

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