<?php

require_once('anvilContainer.class.php');


/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilTabPanelTab extends anvilContainer
{

    public $active = false;
    public $title;
    public $url;


    public function __construct($title = '', $url = '', $active = false, $properties = null)
    {

        parent::__construct();

        $this->active = $active;
        $this->title = $title;
        $this->url = $url;
    }

    public function renderTab()
    {
        $return = '<li';
        if ($this->active) {
            $return .= ' class="active"';
        }
        $return .= '>';
        
        if (!empty($this->url)) {
            $return .= '<a href="' . $this->url . '">';
        } else {
            $return .= '<a href="">';

        }
        
        return $return;
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
        $return .= '<div class="header">';
        $return .= '<h2>' . $this->title . '</h2>';

        //---- Actions
        $return .= '<div class="actions">';
        $return .= $this->headerActions->renderControls();
        $return .= '</div>';

        $return .= '</div>';

        //---- Body ------------------------------------------------------------
        $return .= '<div class="body">';
        $return .= $this->renderControls();
        $return .= '</div>';

        //---- Footer ----------------------------------------------------------
        $return .= '<div class="footer">';
        $return .= $this->footer->renderControls();
        $return .= '</div>';

        $return .= '</div>';


        return $return;
    }
}

?>