<?php

require_once 'anvilFormControl.abstract.php';


/**
 * Multi-Lined Text Entry Control
 *
 * @copyright     Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilMemo extends anvilFormControlAbstract
{

    const VERSION = '1.0';

    private $_sizeClass = array(
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
        'span12',
        'input-full'
    );

    const SIZE_MINI    = 0;
    const SIZE_SMALL   = 1;
    const SIZE_MEDIUM  = 2;
    const SIZE_LARGE   = 3;
    const SIZE_XLARGE  = 4;
    const SIZE_XXLARGE = 5;
    const SIZE_SPAN1   = 6;
    const SIZE_SPAN2   = 7;
    const SIZE_SPAN3   = 8;
    const SIZE_SPAN4   = 9;
    const SIZE_SPAN5   = 10;
    const SIZE_SPAN6   = 11;
    const SIZE_SPAN7   = 12;
    const SIZE_SPAN8   = 13;
    const SIZE_SPAN9   = 14;
    const SIZE_SPAN10  = 15;
    const SIZE_SPAN11  = 16;
    const SIZE_SPAN12  = 17;
    const SIZE_FULL = 18;

    public $rows;
    public $size = self::SIZE_MEDIUM;
    public $value;


//    public $wrapEnabled = false;


    public function __construct($id = '', $name = '', $size = self::SIZE_MEDIUM, $rows = 3, $value = '', $properties = array())
    {

        $this->rows    = $rows;
        $this->size = $size;
        $this->value   = $value;

        parent::__construct($id, $name, $properties);
    }


    public function renderContent()
    {
        $return = '';

        $return .= '<textarea';

        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        if ($this->name) {
            $return .= ' name="' . $this->name . '"';
        }

//        if ($this->columns) {
//            $return .= ' cols="' . $this->columns . '"';
//        }

        if ($this->rows) {
            $return .= ' rows="' . $this->rows . '"';
        }

        $return .= ' class="' . $this->_sizeClass[$this->size];
        if ($this->class) {
            $return .= ' ' . $this->class;
        }
        $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        $return .= '>';

        if ($this->value) {
            $return .= $this->value;
        }

        $return .= '</textarea>';

        return $return;
    }

}

?>