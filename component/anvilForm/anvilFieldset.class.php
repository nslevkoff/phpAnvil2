<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';


/**
 * Fieldset Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilFieldset extends anvilContainer
{

    public $title;
    public $actions;


    public function __construct($id = '', $title = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->title = $title;

        $this->actions = new anvilContainer();
    }


    public function renderContent()
    {

        $return = '<fieldset';

        if (!empty($this->class)) {
            $return .= ' class="' . $this->class . '"';
        }
        $return .= '>';

        if (!empty($this->title)) {
            $return .= '<legend>' . $this->title . '</legend>';
        }

        $return .= $this->renderControls();

        $actions = $this->actions->renderControls();

        if (!empty($actions)) {
            $return .= '<div class="form-actions">';
            $return .= $actions;
            $return .= '</div>';
        }

        $return .= '</fieldset>';


        return $return;
    }

}

?>