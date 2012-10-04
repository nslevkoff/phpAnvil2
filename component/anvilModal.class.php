<?php

require_once('anvilContainer.class.php');


/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilModal extends anvilContainer
{

    const VERSION = '1.0';

    public $fade = true;
    public $hide = true;
    public $role = 'dialog';
    public $ariaLabelledBy;
    public $ariaHidden = true;

    public $headerClass;
    public $headerStyle;

    public $footerClass;
    public $footerStyle;

//    public $body;
    public $footer;
    public $headerActions;
    public $title;
    public $subTitle;



    public $tabIndex = -1;

    public $headerEnabled = true;
    public $bodyEnabled = true;
    public $footerEnabled = true;


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

        $return .= ' class="modal';

        if ($this->fade) {
            $return .= ' fade';
        }

        if ($this->hide) {
            $return .= ' hide';
        }

        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }

        $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        if ($this->role) {
            $return .= ' role="' . $this->role . '"';
        }

        if ($this->ariaLabelledBy) {
            $return .= ' aria-labelledby="' . $this->ariaLabelledBy . '"';
        }

        if ($this->ariaHidden) {
            $return .= ' aria-hidden="' . $this->ariaHidden . '"';
        }

        $return .= '>';

        //---- Header ----------------------------------------------------------
        if ($this->headerEnabled) {
            $return .= $this->_renderHeader();
        }

        //---- Body ------------------------------------------------------------
        if ($this->bodyEnabled) {
            $return .= $this->_renderBody();
        }

        //---- Footer ----------------------------------------------------------
        if ($this->footerEnabled) {
            $return .= $this->_renderFooter();
        }

        $return .= '</div>';


        return $return;
    }

    protected function _renderHeader()
    {
        $return = '<div class="modal-header';
        if ($this->headerClass) {
            $return .= ' ' . $this->headerClass;
        }
        $return .= '"';

        if ($this->headerStyle) {
            $return .= ' style="' . $this->headerStyle . '"';
        }
        $return .= '>';

        $return .= $this->title;
        if (!empty($this->subTitle)) {
            $return .= ' <small>' . $this->subTitle . '</small>';
        }

        //---- Actions
        $return .= '<div class="actions">';
        $return .= $this->headerActions->renderControls();
        $return .= '</div>';

        $return .= '</div>';

        return $return;
    }

    protected function _renderBody()
    {
        $return = '<div class="modal-body">';
        $return .= $this->renderControls();
        $return .= '</div>';

        return $return;
    }

    protected function _renderFooter()
    {
        $return = '<div class="modal-footer';
        if ($this->footerClass) {
            $return .= ' ' . $this->footerClass;
        }
        $return .= '"';

        if ($this->footerStyle) {
            $return .= ' style="' . $this->footerStyle . '"';
        }
        $return .= '>';

        $return .= $this->footer->renderControls();
        $return .= '</div>';

        return $return;
    }

}

?>