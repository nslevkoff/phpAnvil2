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
class anvilButtonGroup extends anvilContainer
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

    public $align = self::ALIGN_DEFAULT;


    public function __construct($id = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);
    }

    public function addLink($text, $url = '', $active = false, $type = anvilLink::TYPE_BUTTON, $size = anvilLink::SIZE_DEFAULT)
    {
        $objLink = new anvilLink('', $text, $url, $type, $size);

        $this->addControl($objLink);

        return $objLink;
    }

    public function renderContent()
    {

        //---- Opening Tag
        $return = '<div';

        //---- ID
        if (!empty($this->id)) {
            $return .= ' id="' . $this->id . '"';
        }

        //---- Class
        $return .= ' class="btn-group';
        if ($this->align != self::ALIGN_DEFAULT) {
            $return .= ' ' . $this->_alignClass[$this->align];
        }
        $return .= '">';

        $return .= $this->renderControls();

        $return .= '</div>';


        return $return;
    }

}

?>