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
    public $titleActions;


    public function __construct($id = '', $title = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->title = $title;
        $this->titleActions = new anvilContainer();

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
            //---- TODO: Revert back to <legend> tag once Chrome and Safari
            //---- fix their browsers not rendering margins on legend tags
            //---- within fieldsets.
//            $return .= '<legend>' . $this->title;
            $return .= '<div class="legend">' . $this->title;
        }

        $titleActions = $this->titleActions->renderControls();
        if (!empty($titleActions)) {
            $return .= '<div class="actions">';
            $return .= $titleActions;
            $return .= '</div>';
        }

        if (!empty($this->title)) {
//            $return .= '</legend>';
            $return .= '</div>';
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