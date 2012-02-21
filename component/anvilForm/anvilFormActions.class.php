<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';


/**
 * Fieldset Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilFormActions extends anvilContainer
{

    public function __construct($id = '', $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

    }


    public function renderContent()
    {

        $return = '<div class="form-actions">';

        $return .= $this->renderControls();

        $return .= '</div>';

        return $return;
    }

}

?>