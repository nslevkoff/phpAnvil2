<?php
require_once 'anvilControl.abstract.php';


/**
 * Link Control
 *
 * @copyright     Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilLink extends anvilControlAbstract
{

    const VERSION = '1.2';


    //---- Types ---------------------------------------------------------------
    const TYPE_DEFAULT = 0;
    const TYPE_SIMPLE  = 0;
    const TYPE_BUTTON  = 1;
    const TYPE_INFO    = 2;
    const TYPE_SUCCESS = 3;
    const TYPE_WARNING = 4;
    const TYPE_DANGER  = 5;
    const TYPE_PRIMARY = 6;
    const TYPE_INVERSE = 7;

    private $_typeClass = array(
        '',
        '',
        'btn-info',
        'btn-success',
        'btn-warning',
        'btn-danger',
        'btn-primary',
        'btn-inverse'
    );

    //---- Sizes ---------------------------------------------------------------
    const SIZE_DEFAULT = 0;
    const SIZE_MINI    = 1;
    const SIZE_SMALL   = 2;
    const SIZE_LARGE   = 3;

    private $_sizeClass = array(
        '',
        'btn-mini',
        'btn-small',
        'btn-large'
    );

    //---- Properties ----------------------------------------------------------
    public $text;
    public $url;
    public $onClick;
    public $layers;
    public $confirmMessage;

    public $type;
    public $size;


//    public $confirmURL;


    public function __construct($id = '', $text = 'click here', $url = '', $type = self::TYPE_DEFAULT, $size = self::SIZE_DEFAULT, $properties = null)
    {


        parent::__construct($id, $properties);

        $this->text = $text;
        $this->type = $type;
        $this->size = $size;
        $this->url  = $url;
        //		$this->class = $class;
    }


    public function renderContent()
    {
        $render = $this->renderClientScript();
        $render .= "\n";
        $render .= '<a';

        if ($this->id) {
            $render .= ' id="' . $this->id . '"';
        }

        //		$triggers = $this->renderTriggers();
        //		$render .= $triggers;

        if ($this->onClick) {
            $render .= ' onclick="' . $this->onClick . '"';
        }

        if (empty($this->confirmMessage) && $this->url) {
            $render .= ' href="' . $this->url . '"';
        } else {
            $render .= ' href="javascript:void(0);"';
        }

        //---- Class
        $render .= ' class="';
        if ($this->type != self::TYPE_SIMPLE) {
            $render .= 'btn ';


            if (is_numeric($this->type)) {
                $render .= $this->_typeClass[$this->type];

            } else {
                $render .= $this->type;
            }

            $render .= ' ' . $this->_sizeClass[$this->size];
        }

        if ($this->class) {
            $render .= ' ' . $this->class;
        }
        $render .= '"';

        $render .= '>';

        if ($this->layers > 1) {
            $render .= '<span>';
        }

        if ($this->text) {
            $render .= $this->text;
        }

        if ($this->layers > 1) {
            $render .= '</span>';
        }

        $render .= '</a>';

        return $render;
    }


    public function renderClientScript()
    {
        //        global $phpAnvil;

        $return = '';

        if ($this->id && !empty($this->confirmMessage)) {

            $return .= '<script>' . "\n";
            $return .= "\t" . '$(document).ready(function(){' . "\n";
            $return .= "\t\t" . '$("#' . $this->id . '").click(function(e){' . "\n";
            $return .= "\t\t\t" . 'e.preventDefault();' . "\n";
            $return .= "\t\t\t" . 'if(confirm("' . $this->confirmMessage . '"))' . "\n";
            $return .= "\t\t\t" . '{' . "\n";

            //            $url = (!empty($this->confirmURL) ? $this->confirmURL : '#');
            $url = (!empty($this->url)
                    ? $this->url
                    : '#');

            $return .= "\t\t\t\t" . 'document.location = "' . $url . '";' . "\n";
            $return .= "\t\t\t" . '}' . "\n";
            $return .= "\t\t" . '});' . "\n";
            $return .= "\t" . '});' . "\n";
            $return .= '</script>' . "\n";
        }

        return $return;
    }
}

?>