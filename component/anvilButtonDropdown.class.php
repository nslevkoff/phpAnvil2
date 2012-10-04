<?php
require_once 'anvilContainer.class.php';
require_once 'anvilLiteral.class.php';


/**
 * phpAnvil Button Dropdown Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilButtonDropdown extends anvilContainer
{

    public $title;
    public $dropdownClass;


    public function __construct($id = '', $title = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->title      = $title;
    }

    public function addDivider()
    {
        $this->addControl(new anvilLiteral('', '<li class="divider"></li>'));

    }

    public function addLink($text, $url = '', $active = false)
    {
        $objNavItem = new anvilNavItem('', $active);
        $objNavItem->addControl(new anvilLink('', $text, $url));
        $this->addControl($objNavItem);

        return $objNavItem;
    }


    public function addStatusLink($text, $url, $status)
    {
        $icon = '<i class="icon-';
        if ($status) {
            $icon .= 'ok';
        } else {
            $icon .= 'none';
        }
        $icon .= '"></i>&nbsp';

        $return = $this->addLink($icon . $text, $url);
        return $return;
    }


    public function renderContent()
    {

        $return = '<div class="btn-group';
        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }
        $return .= '">';

        //---- Render Button Link ----------------------------------------------
        $return .= '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
        $return .= $this->title;
        $return .= '<span class="caret"></span>';
        $return .= '</a>';

        $return .= '<ul class="dropdown-menu';
        if (!empty($this->dropdownClass)) {
            $return .= ' ' . $this->dropdownClass;
        }
        $return .= '">';

        $return .= $this->renderControls();

        $return .= '</ul>';
        $return .= '</div>';


        return $return;
    }

}

?>