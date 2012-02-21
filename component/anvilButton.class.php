<?php
require_once('anvilControl.abstract.php');


/**
 * Standard Button Control
 *
 * @copyright       Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilButton extends anvilControlAbstract
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
    public $name = 'btn';
    public $size = self::SIZE_DEFAULT;
    public $type;
    public $text;
    public $value = '';


    public function __construct($id = '', $text = 'Submit', $actionType = self::ACTION_TYPE_DEFAULT, $type = self::TYPE_DEFAULT, $size = self::SIZE_DEFAULT, $properties = array())
    {
        $this->actionType = $actionType;
        $this->text = $text;
        $this->type = $type;
        $this->size = $size;

        if ($this->type == self::TYPE_DEFAULT) {
            $this->type = $this->_actionTypeDefault[$this->actionType];
        }

        parent::__construct($id, $properties);
    }


    public function renderContent()
    {
        $return = '<button';

        //---- ID
        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        //---- Class
        $return .= ' class="btn';
        if ($this->actionType != self::ACTION_TYPE_SIMPLE) {
            $return .= ' ' . $this->_sizeClass[$this->size];
            $return .= ' ' . $this->_typeClass[$this->type];
        }

        if ($this->class) {
            $return .= ' ' . $this->class;
        }
        $return .= '"';

        //---- Type
        if ($this->actionType != self::ACTION_TYPE_SIMPLE) {
            $return .= ' type="';
            $return .= $this->_actionTypeText[$this->actionType];
            $return .= '"';

        }


//        if ($this->value) {
//            if ($this->actionType == self::ACTION_TYPE_IMAGE) {
//                $return .= ' src="' . $this->value . '"';
//            } else {
        $return .= ' name="' . $this->name . '"';
        if ($this->value != '') {
            $return .= ' value="' . $this->value . '"';
        } else {
            $return .= ' value="' . $this->text . '"';
        }
//            }
//        }



        if ($this->confirmMsg) {
            $return .= " onclick=\"return confirm('" . $this->confirmMsg . "');\"";
        }

        $return .= ">";

        $return .= $this->text;

        $return .= '</button>';

        return $return;
    }
}

?>