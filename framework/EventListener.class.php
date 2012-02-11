<?php

class EventListener {
	public $event = '';
    public $callback = '';

    function __construct($event, $callback)
    {
        $this->event = $event;
        $this->callback = $callback;
    }
}

?>
