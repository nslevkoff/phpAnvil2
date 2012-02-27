<?php

require_once('anvilContainer.class.php');


/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilPanel extends anvilContainer
{

    const VERSION = '1.0';

//    public $body;
    public $footer;
    public $headerActions;
    public $title;
    public $subTitle;


    public function __construct($id = 0, $title = '', $properties = null)
    {

        parent::__construct($id, $properties);

        $this->headerActions = new anvilContainer();
//        $this->body = new anvilContainer();
        $this->footer = new anvilContainer();
        $this->title = $title;
    }


    public function renderContent()
    {

        //---- Wrapper ---------------------------------------------------------
        $return = '<div';

        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        $return .= ' class="panel';

        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }

        $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        $return .= '>';

        //---- Header ----------------------------------------------------------
        $return .= $this->_renderHeader();

        //---- Body ------------------------------------------------------------
        $return .= $this->_renderBody();

        //---- Footer ----------------------------------------------------------
        $return .= $this->_renderFooter();

        $return .= '</div>';


        return $return;
    }

    protected function _renderHeader()
    {
        $return = '<div class="header">';
        $return .= '<h2>' . $this->title;
        if (!empty($this->subTitle)) {
            $return .= ' <small>' . $this->subTitle . '</small>';
        }
        $return .= '</h2>';

        //---- Actions
        $return .= '<div class="actions">';
        $return .= $this->headerActions->renderControls();
        $return .= '</div>';

        $return .= '</div>';

        return $return;
    }

    protected function _renderBody()
    {
        $return = '<div class="body">';
        $return .= $this->renderControls();
        $return .= '</div>';

        return $return;
    }

    protected function _renderFooter()
    {
        $return = '<div class="footer">';
        $return .= $this->footer->renderControls();
        $return .= '</div>';

        return $return;
    }

}

?>