<?php

require_once 'anvilPanel.class.php';

require_once 'anvilTabs.class.php';

/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilTabPanel extends anvilPanel
{

    const VERSION = '1.0';

    /**
     * @var anvilTabs
     */
    public $tabs;

    public function __construct($id = 0, $title = '', $position = anvilTabs::POSITION_DEFAULT, $properties = null)
    {

        parent::__construct($id, $title, $properties);

        $this->class .= ' tabPanel';

        $this->tabs = new anvilTabs('', $position);
//        $this->addControl($this->tabs);
    }


    public function addHeader($title)
    {
        return $this->tabs->addHeader($title);
    }


    /**
     * @param string $id
     * @param string $title
     * @param string $url
     * @param bool $active
     * @return anvilTabItem
     */
    public function addTab($id, $title, $url = '', $active = false, $properties = null)
    {
        return $this->tabs->addTab($id, $title, $url, $active, $properties);
    }

    protected function _renderBody()
    {
        $return = $this->tabs->render();

//        $return .= '<div class="body">';
//        $return .= $this->renderControls();
//        $return .= '</div>';

        return $return;
    }

}

?>