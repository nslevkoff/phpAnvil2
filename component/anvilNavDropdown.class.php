<?php
require_once 'anvilContainer.class.php';
require_once 'anvilLiteral.class.php';


/**
 * phpAnvil Nav Item Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilNavDropdown extends anvilContainer
{

    public $title;

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


    public function renderContent()
    {

        $return = '<li class="dropdown">';

        $return .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
        $return .= $this->title;
        $return .= '<b class="caret"></b>';
        $return .= '</a>';

        $return .= '<ul class="dropdown-menu">';

        $return .= $this->renderControls();

        $return .= '</ul>';
        $return .= '</li>';


        return $return;
    }

}

?>