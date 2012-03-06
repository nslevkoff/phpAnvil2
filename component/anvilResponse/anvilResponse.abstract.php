<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';

/**
 * phpAnvil Response Abstract Control
 *
 * @copyright       Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
abstract class anvilResponseAbstract extends anvilContainer
{

    const VERSION = '1.0';

    public function __construct($properties = null)
    {
        parent::__construct(0, $properties);
    }

}

?>