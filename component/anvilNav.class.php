<?php
require_once 'anvilContainer.class.php';
require_once 'anvilLink.class.php';
require_once 'anvilLiteral.class.php';
require_once 'anvilNavDropdown.class.php';
require_once 'anvilNavItem.class.php';


/**
 * phpAnvil Nav Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilNav extends anvilContainer
{

    //---- Align ---------------------------------------------------------------
    const ALIGN_DEFAULT = 0;
    const ALIGN_LEFT  = 1;
    const ALIGN_RIGHT = 2;

    private $_alignClass = array(
        '',
        'pull-left',
        'pull-right'
    );

    //---- Types ---------------------------------------------------------------
    const TYPE_DEFAULT = 0;
    const TYPE_SIMPLE = 0;
    const TYPE_LIST = 1;
    const TYPE_PILLS  = 2;
    const TYPE_TABS = 3;

    private $_typeClass = array(
        '',
        'nav-list',
        'nav-pills',
        'nav-tabs'
    );

    public $align = self::ALIGN_DEFAULT;
    public $type = self::TYPE_DEFAULT;
    public $stacked = false;


    public function __construct($id = '', $type = self::TYPE_DEFAULT, $align = self::ALIGN_DEFAULT, $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->align      = $align;
        $this->type = $type;
    }

    public function addDivider()
    {
        $this->addControl(new anvilLiteral('', '<li class="divider-vertical"></li>'));

    }

    public function addDropdown($title)
    {
        $objNavDropdown = new anvilNavDropdown('', $title);
        $this->addControl($objNavDropdown);

        return $objNavDropdown;
    }

    public function addLink($text, $url = '#', $active = false, $type = anvilLink::TYPE_DEFAULT, $size = anvilLink::SIZE_DEFAULT, $properties = null)
    {
        $objNavItem = new anvilNavItem('', $active);
        $objNavItem->addControl(new anvilLink('', $text, $url, $type, $size, $properties));
        $this->addControl($objNavItem);

        return $objNavItem;
    }

    public function renderContent()
    {

        //---- Opening Tag
        $return = '<ul';

        //---- ID
        if (!empty($this->id)) {
            $return .= ' id="' . $this->id . '"';
        }

        //---- Class
        $return .= ' class="nav';
        $return .= ' ' . $this->_typeClass[$this->type];
        $return .= ' ' . $this->_alignClass[$this->align];
        if ($this->stacked) {
            $return .= ' nav-stacked';
        }
        $return .= '">';

        $return .= $this->renderControls();

        $return .= '</ul>';


        return $return;
    }

}

?>