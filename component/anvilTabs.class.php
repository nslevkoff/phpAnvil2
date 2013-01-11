<?php

require_once 'anvilContainer.class.php';

require_once 'anvilTabItem.class.php';

/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilTabs extends anvilContainer
{

    const VERSION = '1.0';

    const POSITION_DEFAULT = 0;
    const POSITION_TOP     = 0;
    const POSTIION_RIGHT   = 1;
    const POSITION_BOTTOM  = 2;
    const POSITION_LEFT    = 3;

    private $_positionClass = array(
        '',
        'tabs-right',
        'tabs-below',
        'tabs-left'
    );

    /**
     * @var anvilContainer
     */
    protected $_tabs;

    public $position = self::POSITION_DEFAULT;


    public function __construct($id = 0, $position = self::POSITION_DEFAULT, $properties = null)
    {

        parent::__construct($id, $properties);

        $this->enableLog();


        $this->_tabs = new anvilContainer();

        $this->position = $position;
    }


    public function addHeader($title)
    {
        $objTab = new anvilTabItem('', $title);
        $objTab->type = anvilTabItem::TYPE_HEADER;

        $this->_tabs->addControl($objTab);

        return $objTab;
    }


    /**
     * @param string $id
     * @param string $title
     * @param string $url
     * @param bool   $active
     *
     * @return anvilTabItem
     */
    public function addTab($id, $title, $url = '', $active = false, $properties = null)
    {
        $objTab = new anvilTabItem($id, $title, $url, $active, $properties);

        $this->_tabs->addControl($objTab);

        return $objTab;
    }


    protected function _renderTabs()
    {
        $tabID = 0;

        $return = '';

        for ($this->_tabs->controls->moveFirst(); $this->_tabs->controls->hasMore(); $this->_tabs->controls->moveNext()) {

            $objTab = $this->_tabs->controls->current();
            /** @var $objTab anvilTabItem  */

            $return .= '<li';

            if ($objTab->type == anvilTabItem::TYPE_HEADER) {
                $return .= ' class="nav-header">';
                $return .= $objTab->title;

            } else {
                $class = '';

                if ($objTab->class) {
                    $class .= $objTab->class;
                }

                if ($objTab->active) {
                    $class .= ' active';
                }

                if ($class) {
                    $return .= ' class="' . $class . '"';
                }
                $return .= '>';

                if (!empty($objTab->url)) {
                    $return .= '<a href="' . $objTab->url . '">';
                } else {
                    $tabID++;
                    $return .= '<a href="#' . $objTab->id . '" data-toggle="tab">';
                }

                $return .= $objTab->title;

                if (isset($objTab->rightCounter) || !empty($objTab->rightIcon)) {
                    $return .= ' <span class="pull-right">';

                    if (isset($objTab->rightCounter)) {
                        $return .= '<span class="badge">' . $objTab->rightCounter . '</span>';
                    }

                    if (!empty($objTab->rightIcon)) {
                        $return .= '<i class="' . $objTab->rightIcon . '"></i>';
                    } else {
                        $return .= '<i class="icon-none icon-caret-right"></i>';
                    }

                    $return .= '</span>';
                }
                $return .= '</a>';
            }
            $return .= '</li>';

        }

        return $return;
    }


    protected function _renderTabPanels()
    {
        $tabID = 0;

        $return = '';

        for ($this->_tabs->controls->moveFirst(); $this->_tabs->controls->hasMore(); $this->_tabs->controls->moveNext()) {

            $objTab = $this->_tabs->controls->current();
            /** @var $objTab anvilTabPanelTab  */

            if (empty($objTab->url)) {
                $tabID++;

                $return .= '<div class="tab-pane';
                if ($objTab->active) {
                    $return .= ' active';
                }
                $return .= '"';
                $return .= ' id="' . $objTab->id . '"';
                $return .= '>';

                $return .= $objTab->renderControls();

                $return .= '</div>';
            }
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

        $return .= ' class="tabbable';

        $return .= ' ' . $this->_positionClass[$this->position];

        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }

        $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        $return .= '>';

        //---- Tab Links -------------------------------------------------------
//        $return .= '<div class="tab-bar">';
        $return .= '<ul class="nav nav-tabs tab-bar">';
        $return .= $this->_renderTabs();
        $return .= '</ul>';
//        $return .= '</div>';

        //---- Tab Content -----------------------------------------------------
        $return .= '<div class="tab-content">';
        $return .= $this->_renderTabPanels();
        $return .= '</div>';

        $return .= '</div>';

        return $return;
    }
}

?>