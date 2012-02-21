<?php
require_once 'anvilContainer.class.php';


/**
 * phpAnvil Nav Item Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilNavItem extends anvilContainer
{

    public $active = false;


    public function __construct($id = '', $active = false, $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->active      = $active;
    }


    public function renderContent()
    {

        //---- Opening Tag
        $return = '<li';

        //---- Class
        if ($this->active) {
            $return .= ' class="';
            $return .= 'active';
            $return .= '"';
        }
        $return .= '>';

        $return .= $this->renderControls();

        $return .= '</li>';


        return $return;
    }

}

?>