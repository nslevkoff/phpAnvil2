<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';


/**
 * Fieldset Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilControlGroup extends anvilContainer
{

    public $label;
    public $labelForID;

    public function __construct($id = '', $label = '', $labelForID = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->label = $label;
        $this->labelForID = $labelForID;
    }


    public function renderContent()
    {

        $return = '<div class="control-group';
        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }
        $return .= '">';

        if (!empty($this->label)) {
            $return .= '<label class="control-label" for="' . $this->labelForID . '">' . $this->label . '</label>';
        }

        $return .= '<div class="controls">';

        $return .= $this->renderControls();

        $return .= '</div>';
        $return .= '</div>';


        return $return;
    }

}

?>