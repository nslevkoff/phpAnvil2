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
    public $linkClass;

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

    public function addLink($text, $url = '', $active = false, $properties = null)
    {
        $objNavItem = new anvilNavItem('', $active);
        $objNavItem->addControl(new anvilLink('', $text, $url, anvilLink::TYPE_DEFAULT, anvilLink::SIZE_DEFAULT, $properties));
        $this->addControl($objNavItem);

        return $objNavItem;
    }


    public function renderContent()
    {

        $return = '<li class="dropdown';

        if ($this->class) {
            $return .= ' ' . $this->class;
        }
        $return .= '">';

        $return .= '<a href="#" class="dropdown-toggle';
        if ($this->linkClass) {
            $return .= ' ' . $this->linkClass;
        }
        $return .= '" data-toggle="dropdown">';
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