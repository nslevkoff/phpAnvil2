<?php

require_once 'anvilContainer.class.php';


/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilTabItem extends anvilContainer
{

    public $active = false;
    public $title;
    public $url;


    public function __construct($id, $title = '', $url = '', $active = false, $properties = null)
    {

        parent::__construct($id, $properties);

        $this->active = $active;
        $this->title = $title;

        if (!$this->active) {
            $this->url = $url;
        }
    }
}

?>