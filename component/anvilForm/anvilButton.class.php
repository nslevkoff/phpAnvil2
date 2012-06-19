<?php
/**
 * @file
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                  This source file is subject to the new BSD license that is
 *                  bundled with this package in the file LICENSE.txt. It is also
 *                  available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup         phpAnvilTools
 */


require_once('anvilFormControl.abstract.php');


/**
 * Standard Form Based Button Control
 *
 * @version         1.0
 * @date            8/26/2010
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools
 */
class anvilButton extends anvilFormControlAbstract
{

    const VERSION = '1.0';


    //---- Action Types --------------------------------------------------------
    const ACTION_TYPE_DEFAULT = 0;
    const ACTION_TYPE_SIMPLE = 0;
    const ACTION_TYPE_SUBMIT = 1;
    const ACTION_TYPE_RESET  = 2;
    const ACTION_TYPE_BUTTON = 3;
    const ACTION_TYPE_IMAGE  = 4;
    const ACTION_TYPE_DELETE = 5;

    private $_actionTypeText = array(
        '',
        'submit',
        'reset',
        'button',
        'image',
        'submit'
    );

    private $_actionTypeDefault = array(
        0,
        1,
        0,
        0,
        0,
        5
    );


    //---- Types ---------------------------------------------------------------
    const TYPE_DEFAULT = 0;
    const TYPE_PRIMARY = 1;
    const TYPE_INFO    = 2;
    const TYPE_SUCCESS = 3;
    const TYPE_WARNING = 4;
    const TYPE_DANGER  = 5;
    const TYPE_INVERSE = 6;

    private $_typeClass = array(
        '',
        'btn-primary',
        'btn-info',
        'btn-success',
        'btn-warning',
        'btn-danger',
        'btn-inverse'
    );

    //---- Sizes ---------------------------------------------------------------
    const SIZE_DEFAULT = 0;
    const SIZE_MINI = 1;
    const SIZE_SMALL = 2;
    const SIZE_LARGE = 3;

    private $_sizeClass = array(
        '',
        'btn-mini',
        'btn-small',
        'btn-large'
    );

    //---- Properties ----------------------------------------------------------
    public $actionType = self::ACTION_TYPE_DEFAULT;
    public $confirmMsg;
    public $size = self::SIZE_DEFAULT;
    public $type;
    public $text;


    public function __construct($id = '', $text = 'Submit', $actionType = self::ACTION_TYPE_DEFAULT, $type = self::TYPE_DEFAULT, $size = self::SIZE_DEFAULT, $properties = array(), $traceEnabled = false)
    {
        $this->actionType = $actionType;
        $this->text = $text;
        $this->type = $type;
        $this->size = $size;

        if ($this->type == self::TYPE_DEFAULT) {
            $this->type = $this->_actionTypeDefault[$this->actionType];
        }

        parent::__construct($id, '', $properties, $traceEnabled);
    }


    public function renderContent()
    {
        $return = '<button';

        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }


        $return .= ' type="';

        $return .= $this->_actionTypeText[$this->actionType];

        $return .= '"';

        if ($this->name) {
            $return .= ' name="' . $this->name . '"';
        }

        if ($this->value) {
            if ($this->actionType == self::ACTION_TYPE_IMAGE) {
                $return .= ' src="' . $this->value . '"';
            } else {
                $return .= ' value="' . $this->value . '"';
            }
        }

        $return .= ' class="';
        $return .= $this->_sizeClass[$this->size] . ' ';

        if (is_numeric($this->type)) {
            $return .= $this->_typeClass[$this->type];

        } else {
            $return .= $this->type;
        }

        if ($this->class) {
            $return .= ' ' . $this->class;
        }
        $return .= '"';


        if ($this->confirmMsg) {
            $return .= " onclick=\"return confirm('" . $this->confirmMsg . "');\"";
        }

        $return .= " />\n";

        return $return;
    }


    public function renderPreClientScript()
    {
        $return = '';
        $return .= parent::renderPreClientScript();
        return $return;
    }


    public function renderPostClientScript()
    {
        $return = '';
        $return .= parent::renderPostClientScript();
        return $return;
    }
}

?>