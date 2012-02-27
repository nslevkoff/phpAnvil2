<?php

require_once('anvilContainer.class.php');


/**
 * phpAnvil Alert Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilAlert extends anvilContainer
{

    const VERSION = '1.0';

    //---- Types ---------------------------------------------------------------
    const TYPE_DEFAULT = 4;
    const TYPE_INFO    = 2;
    const TYPE_SUCCESS = 3;
    const TYPE_WARNING = 4;
    const TYPE_DANGER  = 5;
    const TYPE_ERROR   = 5;

    private $_typeClass = array(
        '','',
        'alert-info',
        'alert-success',
        '',
        'alert-error'
    );


    public $title;
    public $type = self::TYPE_DEFAULT;
    public $message;
    public $block = false;
    public $closeable = true;


    public function __construct($id = 0, $type = self::TYPE_DEFAULT, $title = '', $message = '', $properties = null)
    {

        parent::__construct($id, $properties);

        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }


    public function renderContent()
    {

        //---- Opening Tag
        $return = '<div';

        //---- ID
        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        //---- Class
        $return .= ' class="alert';
        
        $return .= ' ' . $this->_typeClass[$this->type];
        
        if ($this->block) {
            $return .= ' alert-block';
        }

        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }

        $return .= '"';

        //---- Style
        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        $return .= '>';
        
        //---- Close Button
        if ($this->closeable) {
            $return .= '<a class="close" data-dismiss="alert">×</a>';
        }

        //---- Title
        if (!empty($this->title)) {
            $return .= '<h4 class="alert-heading">';
            $return .= $this->title . '</h4>';
        }

        //---- Message
        $return .= $this->message;
        $return .= $this->renderControls();

        $return .= '</div>';

        return $return;
    }
}

?>