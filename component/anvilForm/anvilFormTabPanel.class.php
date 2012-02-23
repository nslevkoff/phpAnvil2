<?php

require_once PHPANVIL2_COMPONENT_PATH . 'anvilTabPanel.class.php';


/**
 * phpAnvil Panel Container Control
 *
 * @copyright       Copyright (c) 2009-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilFormTabPanel extends anvilTabPanel
{

    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_DELETED = 3;

    private $_statusClass = array(
        '',
        '',
        'disabled',
        'deleted'
    );

    public $status = self::STATUS_ACTIVE;


    public function __construct($id = 0, $title = '', $position = anvilTabs::POSITION_DEFAULT, $status = self::STATUS_ACTIVE, $properties = null)
    {

        $this->status = $status;

        switch ($status) {
            case self::STATUS_DISABLED:
                $title .= ' (disabled)';
                break;

            case self::STATUS_DELETED:
                $title .= ' (deleted)';
                break;
        }

        parent::__construct($id, $title, $position, $properties);

        $this->class .= ' ' . $this->_statusClass[$this->status];
    }
}

?>